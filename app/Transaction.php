<?php

namespace App;

use DB;
use Mail;
use Auth;
use Exception;
use App\User;
use Carbon\Carbon;
use App\Application;
use App\Mail\TransactionMail;
use App\Transformers\ApiResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

// use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Transaction extends Model
{

    // use Cachable;
    use SoftDeletes;
    protected $table = 'transactions';
    protected $guarded = [];
    // protected $fillable = [
    //     'user_id', 'order_id', 'session_id', 'gateway_id', 'first_name', 'last_name', 'address', 'customer_order_id', 'country',
    //     'state', 'city', 'zip', 'ip_address', 'email', 'phone_no', 'card_type', 'amount', 'amount_in_usd', 'currency', 'card_no',
    //     'ccExpiryMonth', 'ccExpiryYear', 'cvvNumber', 'status', 'reason', 'descriptor', 'payment_gateway_id', 'payment_type',
    //     'merchant_discount_rate', 'bank_discount_rate', 'net_profit_amount', 'chargebacks', 'chargebacks_date', 'chargebacks_remove',
    //     'chargebacks_remove_date', 'refund', 'refund_reason', 'refund_date', 'refund_remove', 'refund_remove_date', 'is_flagged', 'flagged_by',
    //     'flagged_date', 'is_flagged_remove', 'flagged_remove_date', 'is_retrieval', 'retrieval_date', 'is_retrieval_remove',
    //     'retrieval_remove_date', 'is_converted', 'converted_amount', 'converted_currency', 'is_converted_user_currency', 'converted_user_amount',
    //     'converted_user_currency', 'website_url_id', 'request_from_ip', 'request_origin', 'is_request_from_vt', 'is_transaction_type',
    //     'is_webhook', 'response_url', 'webhook_url', 'webhook_status', 'webhook_retry', 'transactions_token', 'bin_details', 'transaction_hash',
    //     'is_duplicate_delete', 'transaction_date',
    // ];
    protected $dates = ['created_at', 'updated_at', 'chargebacks_date', 'refund_date', 'flagged_date', 'retrieval_date'];
    public function setCardNoAttribute($value)
    {
        $this->attributes['card_no'] = Crypt::encryptString($value);
    }

    public function setccExpiryMonthAttribute($value)
    {
        $this->attributes['ccExpiryMonth'] = Crypt::encryptString($value);
    }

    public function setccExpiryYearAttribute($value)
    {
        $this->attributes['ccExpiryYear'] = Crypt::encryptString($value);
    }

    public function setcvvNumberAttribute($value)
    {
        $this->attributes['cvvNumber'] = Crypt::encryptString($value);
    }

    public function getCardNoAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function getCcExpiryMonthAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function getCcExpiryYearAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function getCvvNumberAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function getRefundsMerchantTransactionData($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select('applications.business_name as userName', 'transactions.*', 'middetails.bank_name')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->where('transactions.chargebacks', '0')
            ->where('transactions.refund', '1');
        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }
        $this->filterTransactionData($input, $data);
        $data = $data->orderBy('transactions.refund_date', 'desc')->paginate($noList);
        return $data;
    }

    public function getChargebacksMerchantTransactionData($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select('applications.business_name as userName', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files', 'middetails.bank_name')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"chargebacks"'));
            })
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->where('transactions.chargebacks', '1');
        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }
        $this->filterTransactionData($input, $data);
        $data = $data->orderBy('transactions.chargebacks_date', 'desc')->paginate($noList);
        return $data;
    }

    public function getPreArbitrationMerchantTransactionData($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select('applications.business_name as userName', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files', 'middetails.bank_name')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"chargebacks"'));
            })
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->where('transactions.is_pre_arbitration', '1');
        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }
        $this->filterTransactionData($input, $data);
        $data = $data->orderBy('transactions.pre_arbitration_date', 'desc')->paginate($noList);
        return $data;
    }

    public function getFlaggedMerchantTransactionData($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select('applications.business_name as userName', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files', 'middetails.bank_name')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"flagged"'));
            })
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->where('transactions.chargebacks', '0')
            ->where('transactions.is_flagged', '1')
            ->where('transactions.is_flagged_remove', '0');

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }
        $this->filterTransactionData($input, $data);
        //echo $data->toSql();exit();
        $data = $data->orderBy('transactions.flagged_date', 'DESC')->paginate($noList);
        return $data;
    }


    public function getMerchantRemovedFlaggedTransactionData($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select('applications.business_name as userName', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"flagged"'));
            })
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->where('transactions.is_flagged', '1')
            ->where('transactions.is_flagged_remove', '1');

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }
        $this->filterTransactionData($input, $data);

        $data = $data->orderBy('transactions.flagged_date', 'DESC')->paginate($noList);

        return $data;
    }

    public function getRetrivalMerchantTransactionData($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select('applications.business_name as userName', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files', 'middetails.bank_name')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"retrieval"'));
            })
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->where('transactions.chargebacks', '0')
            ->where('transactions.is_retrieval', '1');

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }
        $this->filterTransactionData($input, $data);
        $data = $data->orderBy('transactions.flagged_date', 'DESC')->paginate($noList);
        return $data;
    }

    public function getMerchantTestTransactionData($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select('applications.business_name as userName', 'transactions.*')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->whereIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }
        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }
        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }
        if (isset($input['transaction_ref']) && $input['transaction_ref'] != '') {
            $data = $data->where("transactions.customer_order_id", '=', $input['transaction_ref']);
        }
        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') && (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
            $txn_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
            $txn_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $txn_start_date)
                ->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $txn_end_date);
        } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] == '')) {
            $txn_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $txn_start_date);
        } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] == '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
            $txn_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $txn_end_date);
        }
        $data = $data->orderBy('id', 'DESC')->paginate($noList);
        return $data;
    }

    public function getMerchantDeclinedTransactions($input, $noList)
    {
        $data = static::select("transactions.*", "applications.business_name as userName", "middetails.bank_name")
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->where('transactions.status', '0');

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }
        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }
        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }
        if (isset($input['customer_order_id']) && $input['customer_order_id'] != '') {
            $data = $data->where('transactions.customer_order_id', 'like', '%' . $input['customer_order_id'] . '%');
        }
        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = $input['end_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') && (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
            $txn_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
            $txn_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $txn_start_date)
                ->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $txn_end_date);
        } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] == '')) {
            $txn_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $txn_start_date);
        } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] == '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
            $txn_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $txn_end_date);
        }
        $data = $data->orderBy('id', 'DESC')->paginate($noList);
        return $data;
    }
    public function filterTransactionData($input, $data)
    {
        if (isset($input['transactions_document_upload_files']) && $input['transactions_document_upload_files'] == '1') {
            $data = $data->where('transactions_document_upload.files_for', 'chargebacks');
        }

        if (isset($input['transactions_document_upload_files']) && $input['transactions_document_upload_files'] == '0') {
            $data = $data->where('transactions_document_upload.files_for', null);
        }

        if (isset($input['suspicious_transactions_document_upload_files']) && $input['suspicious_transactions_document_upload_files'] == '1') {
            $data = $data->where('transactions_document_upload.files_for', 'flagged');
        }

        if (isset($input['suspicious_transactions_document_upload_files']) && $input['suspicious_transactions_document_upload_files'] == '0') {
            $data = $data->where('transactions_document_upload.files_for', null);
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }
        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }
        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }
        if (isset($input['customer_order_id']) && $input['customer_order_id'] != '') {
            $data = $data->where('transactions.customer_order_id', 'like', '%' . $input['customer_order_id'] . '%');
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        if (isset($input['status']) && $input['status'] != '') {
            if ($input['status'] == 'preArbitration') {
                $data = $data->where('transactions.is_pre_arbitration', '1');
            } else {
                $data = $data->where('transactions.status', $input['status']);
            }
        }
        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }
        if (isset($input['is_white_label']) && $input['is_white_label'] != '') {
            $data = $data->where("transactions.is_white_label", '=', $input['is_white_label']);
        }
        if (isset($input['chargeback']) && $input['chargeback'] == '1') {
            $data = $data->where("transactions.chargebacks", '=', 1)->where("transactions.chargebacks_remove", '=', 0);
        }

        if (isset($input['dispute']) && $input['dispute'] == '1') {
            $data = $data->where("transactions.is_flagged", '=', 1)->where("transactions.is_flagged_remove", '=', 0);
        }

        if (isset($input['refund']) && $input['refund'] == '1') {
            $data = $data->where("transactions.refund", '=', 1)->where("transactions.refund_remove", '=', 0);
        }

        if (isset($input['retrieval']) && $input['retrieval'] == '1') {
            $data = $data->where("transactions.is_retrieval", '=', 1)->where("transactions.is_retrieval_remove", '=', 0);
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') && (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
            $txn_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
            $txn_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $txn_start_date)
                ->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $txn_end_date);
        } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] == '')) {
            $txn_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $txn_start_date);
        } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] == '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
            $txn_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $txn_end_date);
        }
        //refund date filter
        if ((isset($input['refund_start_date']) && $input['refund_start_date'] != '') && (isset($input['refund_end_date']) && $input['refund_end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['refund_start_date']));
            $end_date = date('Y-m-d', strtotime($input['refund_end_date']));

            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.refund_date)'), '<=', $end_date);
        } else if ((isset($input['refund_start_date']) && $input['refund_start_date'] != '') || (isset($input['refund_end_date']) && $input['refund_end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['refund_start_date']));
            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '>=', $start_date);
        } else if ((isset($input['refund_start_date']) && $input['refund_start_date'] == '') || (isset($input['refund_end_date']) && $input['refund_end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['refund_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '<=', $end_date);
        }
        //chargebacks date filter
        if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] != '') && (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['chargebacks_start_date']));
            $end_date = date('Y-m-d', strtotime($input['chargebacks_end_date']));

            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', $end_date);
        } else if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] != '') || (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['chargebacks_start_date']));
            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', $start_date);
        } else if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] == '') || (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['chargebacks_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', $end_date);
        }
        //retrieval date filter
        if ((isset($input['retrieval_start_date']) && $input['retrieval_start_date'] != '') && (isset($input['retrieval_end_date']) && $input['retrieval_end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['retrieval_start_date']));
            $end_date = date('Y-m-d', strtotime($input['retrieval_end_date']));

            $data = $data->where(DB::raw('DATE(transactions.retrieval_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.retrieval_date)'), '<=', $end_date);
        } else if ((isset($input['retrieval_start_date']) && $input['retrieval_start_date'] != '') || (isset($input['retrieval_end_date']) && $input['retrieval_end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['retrieval_start_date']));
            $data = $data->where(DB::raw('DATE(transactions.retrieval_date)'), '>=', $start_date);
        } else if ((isset($input['retrieval_start_date']) && $input['retrieval_start_date'] == '') || (isset($input['retrieval_end_date']) && $input['retrieval_end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['retrieval_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.retrieval_date)'), '<=', $end_date);
        }
        //flagged date filter
        if ((isset($input['dispute']) && $input['dispute'] != '') && (isset($input['dispute_end_date']) && $input['dispute_end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['dispute']));
            $end_date = date('Y-m-d', strtotime($input['dispute_end_date']));

            $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.flagged_date)'), '<=', $end_date);
        } else if ((isset($input['dispute']) && $input['dispute'] != '') || (isset($input['dispute_end_date']) && $input['dispute_end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['dispute']));
            $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '>=', $start_date);
        } else if ((isset($input['dispute']) && $input['dispute'] == '') || (isset($input['dispute_end_date']) && $input['dispute_end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['dispute_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '<=', $end_date);
        }
        //pre arbitration date filter
        if ((isset($input['prearbitration_start_date']) && $input['prearbitration_start_date'] != '') && (isset($input['prearbitration_end_date']) && $input['prearbitration_end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['prearbitration_start_date']));
            $end_date = date('Y-m-d', strtotime($input['prearbitration_end_date']));

            $data = $data->where(DB::raw('DATE(transactions.pre_arbitration_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.pre_arbitration_date)'), '<=', $end_date);
        } else if ((isset($input['prearbitration_start_date']) && $input['prearbitration_start_date'] != '') || (isset($input['prearbitration_end_date']) && $input['prearbitration_end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['prearbitration_start_date']));
            $data = $data->where(DB::raw('DATE(transactions.pre_arbitration_date)'), '>=', $start_date);
        } else if ((isset($input['prearbitration_start_date']) && $input['prearbitration_start_date'] == '') || (isset($input['prearbitration_end_date']) && $input['prearbitration_end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['prearbitration_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.pre_arbitration_date)'), '<=', $end_date);
        }
        return $data;
    }

    public function getData($input)
    {
        if (\Auth::user()->main_user_id != 0 && \Auth::user()->is_sub_user == '1')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;

        $data = static::orderBy('id', 'DESC')
            ->where('user_id', $userID);

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['website_url_id']) && $input['website_url_id'] != '') {
            $data = $data->where('transactions.website_url_id', $input['website_url_id']);
        }

        // if(isset($input['card_no']) && $input['card_no'] != '') {
        //     $data = $data->where('transactions.card_no',  'like', '%' . $input['card_no'] . '%');
        // }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->where('payment_gateway_id', '!=', '41')
            ->where('payment_gateway_id', '!=', '16');


        if (isset($input['card_no']) && $input['card_no'] != '') {
            $filteredTransactions = $data->get()->filter(function ($record) use ($input) {
                if (strpos($record->card_no, $input['card_no']) !== false) {
                    return $record;
                }
            });
            $perPage = 10;
            $currentPage = (!empty($input['page']) ? $input['page'] : 1);
            $pagedData = $filteredTransactions->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $data = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($filteredTransactions), $perPage, $currentPage, ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]);
        } else {
            $data = $data->paginate(10);
        }

        return $data;
    }

    // ================================================
    /* method : getTestTransactionsData
     * @param  :
     * @Description : get test transaction data
     */// ==============================================
    public function getTestTransactionsData($input)
    {
        if (\Auth::user()->main_user_id != 0 && \Auth::user()->is_sub_user == '1')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;

        $data = static::orderBy('id', 'DESC')
            ->where('user_id', $userID);

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
        }
        if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') && (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
            $txn_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
            $txn_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $txn_start_date)
                ->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $txn_end_date);
        } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] == '')) {
            $txn_start_date = date('Y-m-d', strtotime($input['transaction_start_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '>=', $txn_start_date);
        } else if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] == '') || (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
            $txn_end_date = date('Y-m-d', strtotime($input['transaction_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.transaction_date)'), '<=', $txn_end_date);
        }
        $data = $data->where(function ($query) {
            $query->orWhere('transactions.payment_gateway_id', '16')
                ->orWhere('transactions.payment_gateway_id', '41');
        });

        $data = $data->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0');

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $filteredTransactions = $data->get()->filter(function ($record) use ($input) {
                if (strpos($record->card_no, $input['card_no']) !== false) {
                    return $record;
                }
            });
            $perPage = 10;
            $currentPage = (!empty($input['page']) ? $input['page'] : 1);
            $pagedData = $filteredTransactions->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $data = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($filteredTransactions), $perPage, $currentPage, ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]);
        } else {
            $data = $data->paginate(10);
        }

        return $data;
    }

    public function productsWiseTransactionData($input, $key)
    {
        $user = User::where('api_key', $key)->first();
        $productID = Product::where('user_id', $user->id)->pluck('id');
        return static::select('transactions.*', 'product.name as productName')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->join('product', 'product.id', '=', 'transactions.product_id')
            ->where('users.api_key', $key)
            ->whereIn('transactions.product_id', $productID);

        return $data;
    }

    public function getLatestTransactionsDash()
    {

        if (\Auth::user()->main_user_id != '0')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;

        return static::where('user_id', $userID)->whereNotIn('payment_gateway_id', ['1', '2'])
            ->latest()
            ->take(6)
            ->get();
    }

    public function getLatestRefundTransactionsDash()
    {
        if (\Auth::user()->main_user_id != '0')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;

        return static::where('user_id', $userID)
            ->where('refund', '1')
            ->latest()
            ->take(5)
            ->get();
    }

    public function getLatestChargebackTransactionsDash()
    {
        if (\Auth::user()->main_user_id != '0')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;

        return static::where('user_id', $userID)
            ->where('chargebacks', '1')
            ->latest()
            ->take(5)
            ->get();
    }

    public function getSubData($input, $id)
    {
        if (\Auth::user()->main_user_id != 0 && \Auth::user()->is_sub_user == '1')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;

        $data = static::orderBy('id', 'DESC')
            ->where('user_id', $userID);
        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no', 'like', '%' . $input['card_no'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('is_reccuring_transaction_id', $id)
            ->where('is_batch_transaction', '0')
            ->get();

        return $data;
    }

    public function getRecurringTransactionsData($input)
    {
        if (\Auth::user()->main_user_id != 0 && \Auth::user()->is_sub_user == '1')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;

        $data = static::orderBy('id', 'DESC')
            ->where('user_id', $userID);
        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->where('is_recurring', '1');

        $data = $data->get();

        return $data;
    }

    public function getSubTransactionsData($input, $id)
    {
        if (\Auth::user()->main_user_id != 0 && \Auth::user()->is_sub_user == '1')
            $userID = \Auth::user()->main_user_id;
        else
            $userID = \Auth::user()->id;

        $data = static::orderBy('id', 'DESC')
            ->where('user_id', $userID);
        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no', 'like', '%' . $input['card_no'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('is_reccuring_transaction_id', $id)
            ->get();

        return $data;
    }

    public function findData($id)
    {
        $data = static::select('middetails.*', 'transactions.*', 'transaction_session.request_data', 'transaction_session.response_data', 'middetails.descriptor as mid_descriptor')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->leftJoin('transaction_session', 'transaction_session.order_id', 'transactions.order_id')
            ->where('transactions.id', $id)
            ->first();
        return $data;
    }

    public function storeData($input)
    {
        $input['amount'] = $input['user_amount'];
        unset($input['user_amount']);
        if(isset($input["user_first_name"])){
            $input["first_name"] = $input["user_first_name"];
            unset($input['user_first_name']);
        }
        if(isset($input["user_last_name"])){
            $input["last_name"] = $input["user_last_name"];
            unset($input['user_last_name']);
        }
        if(isset($input["user_email"])){
            $input['email'] = $input['user_email'];
            unset($input['user_email']);
        }
        if(isset($input["user_phone_no"])){
            $input["phone_no"] = $input["user_phone_no"];
            unset($input['user_phone_no']);
        }
        if(isset($input["user_address"])){
            $input["address"] = $input["user_address"];
            unset($input['user_address']);
        }
        if(isset($input["user_country"])){
            $input["country"] = $input["user_country"];
            unset($input['user_country']);
        }
        if(isset($input["user_state"])){
            $input["state"] = $input["user_state"];
            unset($input['user_state']);
        }
        if(isset($input["user_city"])){
            $input["city"] = $input["user_city"];
            unset($input['user_city']);
        }
        if(isset($input["user_zip"])){
            $input["zip"] = $input["user_zip"];
            unset($input['user_zip']);
        }
        if(isset($input["bin_number"])){
            unset($input['bin_number']);
        }
        $input["currency"] = $input["user_currency"];
        unset($input['user_currency']);
        if(isset($input["user_order_ref"])){
            $input["customer_order_id"] = $input["user_order_ref"];
            unset($input['user_order_ref']);
        }
        if(isset($input["user_webhook_url"])){
            $input["webhook_url"] = $input["user_webhook_url"];
            unset($input['user_webhook_url']);
        }
        if(isset($input["user_redirect_url"])){
            $input["response_url"] = $input["user_redirect_url"] ?? "";
            unset($input['user_redirect_url']);
        }
        if (isset($input['user_card_no']) && $input['user_card_no'] != null) {
            $input['card_no'] = substr($input['user_card_no'], 0, 6) . 'XXXXXX' . substr($input['user_card_no'], -4);
            $input['cvvNumber'] = 'XXX';
        }

        if(isset($input["user_ccexpiry_month"])){
            $input["ccExpiryMonth"] = $input["user_ccexpiry_month"];
            unset($input['user_ccexpiry_month']);
        }
        if(isset($input["user_ccexpiry_year"])){
            $input["ccExpiryYear"] = $input["user_ccexpiry_year"];
        }
        $fields = Schema::getColumnListing('transactions');
        $input = \Arr::only($input, $fields);

        $input['created_at'] = date('Y-m-d H:i:s');
        $input['updated_at'] = date('Y-m-d H:i:s');
        $input['transaction_date'] = date('Y-m-d H:i:s');
        // check if transaction already completed
        $check_transaction = static::where('session_id', $input['session_id'])
            ->first();

        if (!empty($check_transaction) && !in_array($check_transaction->status, ['2', '7'])) {
            $check_transaction->reason = $input['reason'];
            $check_transaction->gateway_id = $input['gateway_id'];
            $check_transaction->status = $input['status'];
            $check_transaction->update();
            return $check_transaction;
        } elseif (!empty($check_transaction) && in_array($check_transaction->status, ['2', '7'])) {
            $check_transaction->reason = $input['reason'];
            $check_transaction->status = $input['status'];
            $check_transaction->gateway_id = $input['gateway_id'];
            $check_transaction->update();
            $transaction = $check_transaction;
        } else {
            \Log::info(["input" => $input]);
            try {
                $transaction = static::insert($input);
            } catch (\Illuminate\Database\QueryException $ex) {
                $transaction = static::where('session_id', $input['session_id'])
                    ->first();
            } catch (Exception $e) {
            }

        }

        if (isset($input['webhook_url']) && !empty($input['webhook_url']) && !in_array($input['status'], ['2', '7'])) {
            $request_data = ApiResponse::webhook($input);
            try {
                $http_response = postCurlRequest($input['webhook_url'], $request_data);
            } catch (Exception $e) {
                \Log::info(['webhook_' . $input['order_id'] => $e->getMessage()]);
            }
        }
        return $transaction;
    }

    public function updateData($id, $input)
    {
        return static::find($id)->update($input);
    }

    public function destroyData($id)
    {
        return static::where('id', $id)->delete();
    }



    // ================================================
    /*  method : getAdminLineChartData
     * @ param  :
     * @ Description : get line chart transactions data for admin dashboard
     */// ==============================================
    public function getAdminLineChartData($input)
    {
        $start_date = Carbon::now()->subDays(30);
        $end_date = Carbon::now();
        $date_condition = "";
        $user_condition = "";

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $date_condition = "and created_at between $start_date and $end_date";
        } else {

            $date_condition = "and created_at between date_sub(now() , interval 31 day) and now() ";
        }

        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $user_id = $input['user_id'];
            $user_condition = "and user_id = $user_id";
        }
        $table = '';
        $query = <<<SQL
    select  DATE_FORMAT(created_at, '%Y-%c-%e') as day , sum(tx) as user_count
    from

SQL;

        $where = <<<SQL
    where 1
    $user_condition
    $date_condition
    group by 1
SQL;

        $table = 'tx_success';
        $select = $query . $table . $where;
        $successTran = collect(\DB::select($select))->pluck('user_count', 'day');

        $table = 'tx_refunds';
        $select = $query . $table . $where;
        $refundTran = collect(\DB::select($select))->pluck('user_count', 'day');

        $table = 'tx_chargebacks';
        $select = $query . $table . $where;
        $chargebacksTran = collect(\DB::select($select))->pluck('user_count', 'day');

        $table = 'tx_decline';
        $select = $query . $table . $where;
        $failTran = collect(\DB::select($select))->pluck('user_count', 'day');

        $table = 'tx_flagged';
        $select = $query . $table . $where;
        $flaggedTran = collect(\DB::select($select))->pluck('user_count', 'day');

        $data_array = [];
        $i = 0;
        while (
            strtotime($start_date) <= strtotime($end_date)
        ) {
            $start_date = date("Y-n-j", strtotime($start_date));

            // date
            $data_array[$i][] = date("Y-m-d", strtotime($start_date));

            // success value
            if (isset($successTran[$start_date])) {
                $data_array[$i][] = $successTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // failed value
            if (isset($failTran[$start_date])) {
                $data_array[$i][] = $failTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // chargeback value
            if (isset($chargebacksTran[$start_date])) {
                $data_array[$i][] = $chargebacksTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // refund value
            if (isset($refundTran[$start_date])) {
                $data_array[$i][] = $refundTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // flagged value
            if (isset($flaggedTran[$start_date])) {
                $data_array[$i][] = $flaggedTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            $i++;
            $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
        }

        return $data_array;
    }

    // ================================================
    /*  method : getMonthlyChartDataInTransactionChart
     * @ param  :
     * @ Description : get monthly line chart transactions data for admin dashboard
     */// ==============================================
    private function getMonthlyTrans($trans)
    {

        $arr = [];
        $old_k = "0";
        $cur_kv = "0";
        $cur_k = "";
        $count = 0;
        $day = '';

        foreach ($trans as $a) {
            foreach ($a as $k => $v) {
                if ($k == 'currency') {
                    $currency = $v;
                }
                if ($k == 'day') {
                    $day = $v;
                }
                if ($k == 'approved_vol')
                    $approved_vol = $v;
                if ($k == 'declined_vol')
                    $declined_vol = $v;
                if ($k == 'cb_vol')
                    $cb_vol = $v;
                if ($k == 'flagged_vol')
                    $flagged_vol = $v;
                if ($k == 'refund_vol')
                    $refund_vol = $v;
                if ($k == 'approved_tx')
                    $approved_tx = $v;
                if ($k == 'declined_tx')
                    $declined_tx = $v;
                if ($k == 'cb_tx')
                    $cb_tx = $v;
                if ($k == 'flagged_tx')
                    $flagged_tx = $v;
                if ($k == 'refund_tx')
                    $refund_tx = $v;

                if ($k == 'refund_tx') {
                    $tx = $v;
                    $ar = [];
                    $ar['day'] = $day;

                    $ar['approved_vol'] = $approved_vol;
                    $ar['declined_vol'] = $declined_vol;
                    $ar['cb_vol'] = $cb_vol;
                    $ar['flagged_vol'] = $flagged_vol;
                    $ar['refund_vol'] = $refund_vol;

                    $ar['approved_tx'] = $approved_tx;
                    $ar['decline_tx'] = $declined_tx;
                    $ar['cb_tx'] = $cb_tx;
                    $ar['flagged_tx'] = $flagged_tx;
                    $ar['refund_tx'] = $refund_tx;

                    if (!isset($arr[$currency]))
                        $arr[$currency] = [];
                    if (!isset($arday[$day]))
                        $arday[$day] = [];
                    //array_push($arr[$currency], $ar);
                    //array_push($arday[$day], $ar);
                    array_push($arr[$currency], $ar);
                }
            }
        }

        return $arr;
    }


    public function getMonthlyChartDataInTransactionChart2($input)
    {
        $date_condition = "";
        $user_condition = "";
        if ($input['type'] == 'monthly') {
            $date_fmt = "'%Y-%m'";
        } else if ($input['type'] == 'yearly') {
            $date_fmt = "'%Y-%m-%d'";
        } else {
            $date_fmt = "'%Y-%m-%d'";
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $date_condition = "and created_at between '" . $start_date . "' and '" . $end_date . "' ";
        }


        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $user_id = $input['user_id'];
            //$user_condition = "and user_id = $user_id";
        }
        $table = '';

        // $query = <<<SQL
        //     select  currency, DATE_FORMAT(created_at,"%Y-%m") as day, sum(volume) volume, sum(tx) as tx
        //     from

        // SQL;

        $query = <<<SQL
    select
    currency, day,

    sum(volume) as total_vol,
    sum(tx) as total_tx,

    sum(case when STAT = 'APPROVED' then volume else 0.00 end) as approved_vol,
    sum(case when STAT = 'APPROVED' then tx else 0 end) as approved_tx,
    sum(case when STAT = 'DECLINED' then volume else 0.00 end) as declined_vol,
    sum(case when STAT = 'DECLINED' then tx else 0 end) as declined_tx,
    sum(case when STAT = 'CHARGEBACK' then volume else 0.00 end) as cb_vol,
    sum(case when STAT = 'CHARGEBACK' then tx else 0 end) as cb_tx,
    sum(case when STAT = 'PENDING' then volume else 0.00 end) as pending_vol,
    sum(case when STAT = 'PENDING' then tx else 0 end) as pending_tx,
    sum(case when STAT = 'FLAGGED' then volume else 0.00 end) as flagged_vol,
    sum(case when STAT = 'FLAGGED' then tx else 0 end) as flagged_tx,
    sum(case when STAT = 'REFUND' then volume else 0.00 end) as refund_vol,
    sum(case when STAT = 'REFUND' then tx else 0 end) as refund_tx

from
(
    select  'APPROVED' as STAT, currency, DATE_FORMAT(created_at,       $date_fmt) as day, volume volume, tx as tx, created_at
    from
    tx_success
    union
    select  'DECLINED' as STAT, currency, DATE_FORMAT(created_at,    $date_fmt) as day, volume volume, tx as tx, created_at
    from
    tx_decline
    union
    select  'CHARGEBACK' as STAT, currency, DATE_FORMAT(created_at,     $date_fmt) as day, volume volume, tx as tx, created_at
    from
    tx_chargebacks
    union
    select  'PENDING' as STAT, currency, DATE_FORMAT(created_at,        $date_fmt) as day, volume volume, tx as tx, created_at
    from
    tx_pending
    union
    select  'FLAGGED' as STAT, currency, DATE_FORMAT(created_at,        $date_fmt) as day, volume volume, tx as tx, created_at
    from
    tx_flagged
    union
    select  'REFUND' as STAT, currency, DATE_FORMAT(created_at,         $date_fmt) as day, volume volume, tx as tx, created_at
    from
    tx_refunds
) a

SQL;

        $where = <<<SQL
    where 1
    $user_condition
    $date_condition
    and currency in ('USD', 'GBP', 'EUR')
    group by 1,2
    order by 1, 2
SQL;

        $select = $query . $where;
        $query = \DB::select($select);
        $array = (array) json_decode(json_encode($query), true);

        //dd($input);
        // if(isset($input['type']) && isset($input['filter']))
        // {

        //     $arr = $this->getMonthlyTrans($array);
        // }
        //dd($input);
        if ($input['type'] == 'daily' or (isset($input['filter']) && $input['ftype'] == "fdaily")) {
            $usdcurrencydataarr = array();
            $gbpcurrencydataarr = array();
            $eurcurrencydataarr = array();
            foreach ($array as $arrayusd) {
                if ($arrayusd['currency'] == 'USD') {
                    $usdcurrencydataarr[] = $arrayusd['day'];
                }
                if ($arrayusd['currency'] == 'GBP') {
                    $gbpcurrencydataarr[] = $arrayusd['day'];
                }
                if ($arrayusd['currency'] == 'EUR') {
                    $eurcurrencydataarr[] = $arrayusd['day'];
                }
            }

            $monthstartdate = strtotime($input['sdate_string']);
            $monthenddate = strtotime($input['edate_string']);

            while ($monthstartdate <= $monthenddate) {
                if (!in_array(date('Y-m-d', $monthstartdate), $usdcurrencydataarr)) {
                    $array[] = array('currency' => 'USD', 'day' => date('Y-m-d', $monthstartdate), 'total_vol' => '2012.13', 'total_tx' => '0', 'approved_vol' => '0.00', 'approved_tx' => '0', 'declined_vol' => '0.00', 'declined_tx' => '0', 'cb_vol' => '0.00', 'cb_tx' => '0', 'pending_vol' => '0.00', 'pending_tx' => '0', 'flagged_vol' => '0.00', 'flagged_tx' => '0', 'refund_vol' => '0.00', 'refund_tx' => '0');
                }
                if (!in_array(date('Y-m-d', $monthstartdate), $gbpcurrencydataarr)) {
                    $array[] = array('currency' => 'GBP', 'day' => date('Y-m-d', $monthstartdate), 'total_vol' => '2012.13', 'total_tx' => '0', 'approved_vol' => '0.00', 'approved_tx' => '0', 'declined_vol' => '0.00', 'declined_tx' => '0', 'cb_vol' => '0.00', 'cb_tx' => '0', 'pending_vol' => '0.00', 'pending_tx' => '0', 'flagged_vol' => '0.00', 'flagged_tx' => '0', 'refund_vol' => '0.00', 'refund_tx' => '0');
                }
                if (!in_array(date('Y-m-d', $monthstartdate), $eurcurrencydataarr)) {
                    $array[] = array('currency' => 'EUR', 'day' => date('Y-m-d', $monthstartdate), 'total_vol' => '2012.13', 'total_tx' => '0', 'approved_vol' => '0.00', 'approved_tx' => '0', 'declined_vol' => '0.00', 'declined_tx' => '0', 'cb_vol' => '0.00', 'cb_tx' => '0', 'pending_vol' => '0.00', 'pending_tx' => '0', 'flagged_vol' => '0.00', 'flagged_tx' => '0', 'refund_vol' => '0.00', 'refund_tx' => '0');
                }
                $monthstartdate += 86400;
            }
            usort($array, function ($a, $b) {
                return $a['day'] <=> $b['day'];
            });
            $object = (object) $array;
            $arr = $this->getMonthlyTrans($array);
        } else if ($input['type'] == 'monthly' or (isset($input['filter']) && $input['ftype'] == "fmonthly")) {
            $usdcurrencydataarr = array();
            $gbpcurrencydataarr = array();
            $eurcurrencydataarr = array();

            foreach ($array as $arrayusd) {
                if ($arrayusd['currency'] == 'USD') {
                    $usdcurrencydataarr[] = $arrayusd['day'];
                }
                if ($arrayusd['currency'] == 'GBP') {
                    $gbpcurrencydataarr[] = $arrayusd['day'];
                }
                if ($arrayusd['currency'] == 'EUR') {
                    $eurcurrencydataarr[] = $arrayusd['day'];
                }
            }
            // echo "<pre>";
            // print_r( $usdcurrencydataarr);
            // print_r( $gbpcurrencydataarr);
            // print_r( $eurcurrencydataarr);
            // die;
            $start = $input['sdate_string'];
            $end = $input['edate_string'];
            $monthstartdate = strtotime($start);
            $monthenddate = strtotime($end);

            //dd($start,$end);
            // dd($end);

            while ($monthstartdate <= $monthenddate) {
                if (!in_array(date('Y-m', $monthstartdate), $usdcurrencydataarr)) {
                    $array[] = array('currency' => 'USD', 'day' => date('Y-m', $monthstartdate), 'total_vol' => '2012.13', 'total_tx' => '0', 'approved_vol' => '0.00', 'approved_tx' => '0', 'declined_vol' => '0.00', 'declined_tx' => '0', 'cb_vol' => '0.00', 'cb_tx' => '0', 'pending_vol' => '0.00', 'pending_tx' => '0', 'flagged_vol' => '0.00', 'flagged_tx' => '0', 'refund_vol' => '0.00', 'refund_tx' => '0');
                }
                if (!in_array(date('Y-m', $monthstartdate), $gbpcurrencydataarr)) {
                    $array[] = array('currency' => 'GBP', 'day' => date('Y-m', $monthstartdate), 'total_vol' => '2012.13', 'total_tx' => '0', 'approved_vol' => '0.00', 'approved_tx' => '0', 'declined_vol' => '0.00', 'declined_tx' => '0', 'cb_vol' => '0.00', 'cb_tx' => '0', 'pending_vol' => '0.00', 'pending_tx' => '0', 'flagged_vol' => '0.00', 'flagged_tx' => '0', 'refund_vol' => '0.00', 'refund_tx' => '0');
                }
                if (!in_array(date('Y-m', $monthstartdate), $eurcurrencydataarr)) {

                    $array[] = array('currency' => 'EUR', 'day' => date('Y-m', $monthstartdate), 'total_vol' => '2012.13', 'total_tx' => '0', 'approved_vol' => '0.00', 'approved_tx' => '0', 'declined_vol' => '0.00', 'declined_tx' => '0', 'cb_vol' => '0.00', 'cb_tx' => '0', 'pending_vol' => '0.00', 'pending_tx' => '0', 'flagged_vol' => '0.00', 'flagged_tx' => '0', 'refund_vol' => '0.00', 'refund_tx' => '0');
                }

                $monthstartdate = strtotime("+1 months", $monthstartdate);
            }
            usort($array, function ($a, $b) {
                return $a['day'] <=> $b['day'];
            });
            $object = (object) $array;
            $arr = $this->getMonthlyTrans($array);
        } else if ($input['type'] == 'yearly' or (isset($input['filter']) && $input['ftype'] == "fyearly")) {

            $usdcurrencydataarr = array();
            $gbpcurrencydataarr = array();
            $eurcurrencydataarr = array();
            foreach ($array as $arrayusd) {
                if ($arrayusd['currency'] == 'USD') {
                    $usdcurrencydataarr[] = $arrayusd['day'];
                }
                if ($arrayusd['currency'] == 'GBP') {
                    $gbpcurrencydataarr[] = $arrayusd['day'];
                }
                if ($arrayusd['currency'] == 'EUR') {
                    $eurcurrencydataarr[] = $arrayusd['day'];
                }
            }

            $start = $input['sdate_string'];
            $end = $input['edate_string'];
            $monthstartdate = strtotime($start);
            $monthenddate = strtotime($end);


            while ($monthstartdate <= $monthenddate) {

                if (!in_array(date('Y-m-d', $monthstartdate), $usdcurrencydataarr)) {
                    $array[] = array('currency' => 'USD', 'day' => date('Y-m-d', $monthstartdate), 'total_vol' => '2012.13', 'total_tx' => '0', 'approved_vol' => '0.00', 'approved_tx' => '0', 'declined_vol' => '0.00', 'declined_tx' => '0', 'cb_vol' => '0.00', 'cb_tx' => '0', 'pending_vol' => '0.00', 'pending_tx' => '0', 'flagged_vol' => '0.00', 'flagged_tx' => '0', 'refund_vol' => '0.00', 'refund_tx' => '0');
                }

                if (!in_array(date('Y-m-d', $monthstartdate), $gbpcurrencydataarr)) {
                    $array[] = array('currency' => 'GBP', 'day' => date('Y-m-d', $monthstartdate), 'total_vol' => '2012.13', 'total_tx' => '0', 'approved_vol' => '0.00', 'approved_tx' => '0', 'declined_vol' => '0.00', 'declined_tx' => '0', 'cb_vol' => '0.00', 'cb_tx' => '0', 'pending_vol' => '0.00', 'pending_tx' => '0', 'flagged_vol' => '0.00', 'flagged_tx' => '0', 'refund_vol' => '0.00', 'refund_tx' => '0');
                }

                if (!in_array(date('Y-m-d', $monthstartdate), $eurcurrencydataarr)) {

                    $array[] = array('currency' => 'EUR', 'day' => date('Y-m-d', $monthstartdate), 'total_vol' => '2012.13', 'total_tx' => '0', 'approved_vol' => '0.00', 'approved_tx' => '0', 'declined_vol' => '0.00', 'declined_tx' => '0', 'cb_vol' => '0.00', 'cb_tx' => '0', 'pending_vol' => '0.00', 'pending_tx' => '0', 'flagged_vol' => '0.00', 'flagged_tx' => '0', 'refund_vol' => '0.00', 'refund_tx' => '0');
                }
                $monthstartdate += 86400;
            }

            usort($array, function ($a, $b) {
                return $a['day'] <=> $b['day'];
            });
            $object = (object) $array;
            $arr = $this->getMonthlyTrans($array);
            //dd($arr);
        } else {
            $arr = $this->getMonthlyTrans($array);
        }


        /////

        $query2 = <<<SQL
    select currency,
        sum(approved_vol) approved_vol,
        100*(sum(approved_vol) / sum(total_vol)) as success_percent,

        sum(declined_vol) declined_vol,
        100*(sum(declined_vol) / sum(total_vol)) as declined_percent,

        sum(cb_vol) cb_vol,
        100*(sum(cb_vol) / sum(total_vol)) as cb_percent,

        sum(flagged_vol) flagged_vol,
        100*(sum(flagged_vol) / sum(total_vol)) as flagged_percent,

        sum(refund_vol) refund_vol,
        100*(sum(refund_vol) / sum(total_vol)) as refund_percent,

        sum(total_vol) total_vol
    from
    (
SQL;

        $where2 = <<<SQL
    ) b
    group by currency
    SQL;

        $select2 = $query2 . $select . $where2;
        $query2 = \DB::select($select2);
        $array2 = (array) json_decode(json_encode($query2), true);
        $object2 = (object) $array;
        $arr2 = $array2;

        if (count($arr2) == 0) {
            $defaultArry = ["approved_vol" => "0.00", "success_percent" => "0.000000", "declined_vol" => "0.00", "declined_percent" => "0.000000", "cb_vol" => "0.00", "cb_percent" => "0.000000", "flagged_vol" => "0.00", "flagged_percent" => "0.000000", "refund_vol" => "0.00", "refund_percent" => "0.000000", "total_vol" => "0.00"];
            $curr = ['USD', 'EUR', 'GBP'];
            foreach ($curr as $newcr) {

                $defaultArry['currency'] = $newcr;
                $arr2[] = $defaultArry;
            }
        } else if (count($arr2) < 3) {
            $defaultArry = ["approved_vol" => "0.00", "success_percent" => "0.000000", "declined_vol" => "0.00", "declined_percent" => "0.000000", "cb_vol" => "0.00", "cb_percent" => "0.000000", "flagged_vol" => "0.00", "flagged_percent" => "0.000000", "refund_vol" => "0.00", "refund_percent" => "0.000000", "total_vol" => "0.00"];
            foreach ($arr2 as $newarry) {
                if ($newarry['currency'] !== 'USD') {
                    $defaultArry['currency'] = "USD";
                    $arr2[] = $defaultArry;
                }

                if ($newarry['currency'] !== 'EUR') {
                    $defaultArry['currency'] = "EUR";
                    $arr2[] = $defaultArry;
                }

                if ($newarry['currency'] !== 'GBP') {
                    $defaultArry['currency'] = "GBP";
                    $arr2[] = $defaultArry;
                }
            }
        } else {
            $arr2 = $array2;
        }


        $data_array = array("transactions" => $arr, "totals" => $arr2);
        //dd($data_array);
        return $data_array;
    }

    public function getMonthlyChartDataInTransactionChart($input)
    {
        $start_date = Carbon::now()->subYear();
        $end_date = Carbon::now();

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));
        }

        $successTran = \DB::table('transactions')
            ->select(\DB::raw('DATE_FORMAT(created_at,"%Y-%c") as day'), \DB::raw('count(*) as user_count'))
            ->where('status', '1')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->where('transactions.is_retrieval', '0')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('created_at', [$start_date, $end_date]);
        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $successTran = $successTran->where('transactions.user_id', $input['user_id']);
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $successTran = $successTran->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        $successTran = $successTran->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->groupBy(\DB::raw('DATE_FORMAT(created_at,"%Y-%m")'))
            ->pluck('user_count', 'day');

        //dd($successTran);

        $refundTran = \DB::table('transactions')
            ->select(\DB::raw('DATE_FORMAT(created_at,"%Y-%c") as day'), \DB::raw('count(*) as user_count'))
            ->where('refund', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('created_at', [$start_date, $end_date]);
        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $refundTran = $refundTran->where('transactions.user_id', $input['user_id']);
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $refundTran = $refundTran->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        $refundTran = $refundTran->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->groupBy(\DB::raw('DATE_FORMAT(created_at,"%Y-%m")'))
            ->pluck('user_count', 'day');

        $chargebacksTran = \DB::table('transactions')
            ->select(\DB::raw('DATE_FORMAT(created_at,"%Y-%c") as day'), \DB::raw('count(*) as user_count'))
            ->where('chargebacks', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('created_at', [$start_date, $end_date]);
        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $chargebacksTran = $chargebacksTran->where('transactions.user_id', $input['user_id']);
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $chargebacksTran = $chargebacksTran->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        $chargebacksTran = $chargebacksTran->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->groupBy(\DB::raw('DATE_FORMAT(created_at,"%Y-%m")'))
            ->pluck('user_count', 'day');

        $failTran = \DB::table('transactions')
            ->select(\DB::raw('DATE_FORMAT(created_at,"%Y-%c") as day'), \DB::raw('count(*) as user_count'))
            ->where('status', '0')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('created_at', [$start_date, $end_date]);
        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $failTran = $failTran->where('transactions.user_id', $input['user_id']);
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $failTran = $failTran->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        $failTran = $failTran->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->groupBy(\DB::raw('DATE_FORMAT(created_at,"%Y-%m")'))
            ->pluck('user_count', 'day');

        $flaggedTran = \DB::table('transactions')
            ->select(\DB::raw('DATE_FORMAT(created_at,"%Y-%c") as day'), \DB::raw('count(*) as user_count'))
            ->where('is_flagged', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('created_at', [$start_date, $end_date]);
        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $flaggedTran = $flaggedTran->where('transactions.user_id', $input['user_id']);
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $flaggedTran = $flaggedTran->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        $flaggedTran = $flaggedTran->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->groupBy(\DB::raw('DATE_FORMAT(created_at,"%Y-%m")'))
            ->pluck('user_count', 'day');

        $data_array = [];
        $i = 0;

        while (strtotime($start_date) <= strtotime($end_date)) {

            $start_date = date("Y-n", strtotime($start_date));

            // date
            $data_array[$i][] = date("Y-m-1", strtotime($start_date));

            // success value
            if (isset($successTran[$start_date])) {
                $data_array[$i][] = $successTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // failed value
            if (isset($failTran[$start_date])) {
                $data_array[$i][] = $failTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // chargeback value
            if (isset($chargebacksTran[$start_date])) {
                $data_array[$i][] = $chargebacksTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // refund value
            if (isset($refundTran[$start_date])) {
                $data_array[$i][] = $refundTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // flagged value
            if (isset($flaggedTran[$start_date])) {
                $data_array[$i][] = $flaggedTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            $i++;
            $start_date = date("Y-m-d", strtotime("+1 month", strtotime($start_date)));
        }

        return $data_array;
    }

    public function getChartData($input)
    {
        $userDetails = Auth::user();
        ($userDetails->is_sub_user == 1) ? ($user_id = $userDetails->main_user_id) : ($user_id = Auth::user()->id);

        $successTran = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {

            $successTran = $successTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $successTran = $successTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $successTran = $successTran->where('user_id', $user_id)
            ->where('status', '1')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->where('transactions.is_retrieval', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->count();

        // Success transaction total amount
        $successTranAmount = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {

            $successTranAmount = $successTranAmount->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $successTranAmount = $successTranAmount->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $successTranAmount = $successTranAmount->where('user_id', $user_id)
            ->where('status', '1')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->where('transactions.is_retrieval', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->sum('amount');

        // Declined transaction count
        $failTran = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $failTran = $failTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $failTran = $failTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $failTran = $failTran->where('user_id', $user_id)
            ->where('status', '0')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->count();

        // Declined transaction total amount
        $failTranAmount = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $failTranAmount = $failTranAmount->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $failTranAmount = $failTranAmount->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $failTranAmount = $failTranAmount->where('user_id', $user_id)
            ->where('status', '0')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->count();

        // Chargebacks transaction count
        $chargebacksTran = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $chargebacksTran = $chargebacksTran->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $chargebacksTran = $chargebacksTran->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $chargebacksTran = $chargebacksTran->where('user_id', $user_id)
            ->where('chargebacks', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->count();

        // Chargebacks transaction total amount
        $chargebacksTranAmount = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $chargebacksTranAmount = $chargebacksTranAmount->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $chargebacksTranAmount = $chargebacksTranAmount->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $chargebacksTranAmount = $chargebacksTranAmount->where('user_id', $user_id)
            ->where('chargebacks', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->sum('amount');

        // Refund transaction count
        $refundTran = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $refundTran = $refundTran->where(DB::raw('DATE(transactions.refund_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $refundTran = $refundTran->where(DB::raw('DATE(transactions.refund_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $refundTran = $refundTran->where('user_id', $user_id)
            ->where('refund', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->count();

        // Refund transaction total amount
        $refundTranAmount = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $refundTranAmount = $refundTranAmount->where(DB::raw('DATE(transactions.refund_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $refundTranAmount = $refundTranAmount->where(DB::raw('DATE(transactions.refund_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $refundTranAmount = $refundTranAmount->where('user_id', $user_id)
            ->where('refund', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->sum('amount');

        // Flagged transaction count
        $flaggedTran = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $flaggedTran = $flaggedTran->where(DB::raw('DATE(transactions.flagged_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $flaggedTran = $flaggedTran->where(DB::raw('DATE(transactions.flagged_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $flaggedTran = $flaggedTran->where('user_id', $user_id)
            ->where('is_flagged', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->count();

        // Flagged transaction total amount
        $flaggedTranAmount = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $flaggedTranAmount = $flaggedTranAmount->where(DB::raw('DATE(transactions.flagged_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $flaggedTranAmount = $flaggedTranAmount->where(DB::raw('DATE(transactions.flagged_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $flaggedTranAmount = $flaggedTranAmount->where('user_id', $user_id)
            ->where('is_flagged', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->sum('amount');

        // Pending transaction count
        $pendingTran = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $pendingTran = $pendingTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $pendingTran = $pendingTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $pendingTran = $pendingTran->where('user_id', $user_id)
            ->where('status', '2')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->count();

        // Pending transaction total amount
        $pendingTranAmount = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $pendingTranAmount = $pendingTranAmount->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $pendingTranAmount = $pendingTranAmount->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }
        $pendingTranAmount = $pendingTranAmount->where('user_id', $user_id)
            ->where('status', '2')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->sum('amount');

        // total transaction count and amount
        $totalTran = $successTran + $failTran + $chargebacksTran + $refundTran + $flaggedTran + $pendingTran;
        $totalTranAmount = $successTranAmount + $failTranAmount + $chargebacksTranAmount + $refundTranAmount + $flaggedTranAmount + $pendingTranAmount;

        return [
            'success' => $successTran,
            'fail' => $failTran,
            'chargebacks' => $chargebacksTran,
            'refund' => $refundTran,
            'flagged' => $flaggedTran,
            'pending' => $pendingTran,
            'total' => $totalTran,
            'successamount' => $successTranAmount,
            'failamount' => $failTranAmount,
            'chargebacksamount' => $chargebacksTranAmount,
            'refundamount' => $refundTranAmount,
            'flaggedamount' => $flaggedTranAmount,
            'pendingamount' => $pendingTranAmount,
            'totalamount' => $totalTranAmount,
        ];
    }

    public function getAllUserChargeback($user_id, $input)
    {
        $data = static::select('transactions.*', 'transactions_document_upload.files as transactions_document_upload_files')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"chargebacks"'));
            })->orderBy('transactions.id', 'DESC')
            ->where('transactions.user_id', $user_id);

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('chargebacks', '1');

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->get()->filter(function ($record) use ($input) {
                if (strpos($record->card_no, $input['card_no']) !== false) {
                    return $record;
                }
            });
        } else {
            $data = $data->get();
        }

        return $data;
    }

    public function getAllUserRefunds($user_id, $input)
    {
        $data = static::orderBy('transactions.id', 'DESC')
            ->where('transactions.user_id', $user_id);

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.refund_date)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('refund', '1')
            ->orderBy('transactions.refund_date', 'desc');

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->get()->filter(function ($record) use ($input) {
                if (strpos($record->card_no, $input['card_no']) !== false) {
                    return $record;
                }
            });
        } else {
            $data = $data->get();
        }

        return $data;
    }

    public function getAllUserFlagged($user_id, $input)
    {
        $data = static::select('transactions.*', 'transactions_document_upload.files as transactions_document_upload_files')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"flagged"'));
            })
            ->where('transactions.user_id', $user_id)
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.flagged_date)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('is_flagged', '1')
            ->orderBy('transactions.flagged_date', 'desc');

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->get()->filter(function ($record) use ($input) {
                if (strpos($record->card_no, $input['card_no']) !== false) {
                    return $record;
                }
            });
        } else {
            $data = $data->get();
        }

        return $data;
    }

    public function getUserTransactionReport($input)
    {
        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'CNY'];

        $mainData = [];
        foreach ($currencyArray as $key => $value) {
            // Check Transaction in currency
            $chekTransactionInCurrency = static::where('payment_gateway_id', '<>', '16')
                ->where('payment_gateway_id', '<>', '41')
                ->where('currency', $value)
                ->count();

            if ($chekTransactionInCurrency > 0) {
                $total_approve_transaction_amount = static::where('user_id', \Auth::user()->id)
                    ->where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('is_flagged', '0')
                    ->where('chargebacks', '0')
                    ->where('refund', '0')
                    ->where('is_retrieval', '0')
                    ->where('currency', $value)
                    ->where('status', '1');
                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $start_date = date('Y-m-d', strtotime($input['start_date']));
                    $end_date = date('Y-m-d', strtotime($input['end_date']));

                    $total_approve_transaction_amount = $total_approve_transaction_amount->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                        ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
                }
                $total_approve_transaction_amount = $total_approve_transaction_amount->sum('amount');

                $total_declined_transaction_amount = static::where('user_id', \Auth::user()->id)
                    ->where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('chargebacks', '<>', '1')
                    ->where('refund', '<>', '1')
                    ->where('currency', $value)
                    ->where('status', '0');
                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $start_date = date('Y-m-d', strtotime($input['start_date']));
                    $end_date = date('Y-m-d', strtotime($input['end_date']));

                    $total_declined_transaction_amount = $total_declined_transaction_amount->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                        ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
                }
                $total_declined_transaction_amount = $total_declined_transaction_amount->sum('amount');

                $total_chargebacks_transaction_amount = static::where('user_id', \Auth::user()->id)
                    ->where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $value)
                    ->where('chargebacks', '1');
                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $start_date = date('Y-m-d', strtotime($input['start_date']));
                    $end_date = date('Y-m-d', strtotime($input['end_date']));

                    $total_chargebacks_transaction_amount = $total_chargebacks_transaction_amount->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                        ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
                }
                $total_chargebacks_transaction_amount = $total_chargebacks_transaction_amount->sum('amount');

                $total_refund_transaction_amount = static::where('user_id', \Auth::user()->id)
                    ->where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $value)
                    ->where('refund', '1');
                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $start_date = date('Y-m-d', strtotime($input['start_date']));
                    $end_date = date('Y-m-d', strtotime($input['end_date']));

                    $total_refund_transaction_amount = $total_refund_transaction_amount->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                        ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
                }
                $total_refund_transaction_amount = $total_refund_transaction_amount->sum('amount');

                $total_flagged_amount = static::where('user_id', \Auth::user()->id)
                    ->where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $value)
                    ->where('is_flagged', '1');
                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $start_date = date('Y-m-d', strtotime($input['start_date']));
                    $end_date = date('Y-m-d', strtotime($input['end_date']));

                    $total_flagged_amount = $total_flagged_amount->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                        ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
                }
                $total_flagged_amount = $total_flagged_amount->sum('amount');

                $mainData[$value] = [
                    'total_approve_transaction_amount' => $total_approve_transaction_amount,
                    'total_declined_transaction_amount' => $total_declined_transaction_amount,
                    'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount,
                    'total_refund_transaction_amount' => $total_refund_transaction_amount,
                    'total_flagged_amount' => $total_flagged_amount,
                ];
            }
        }

        return $mainData;
    }

    /*
    |===================================|
    | For A Merchant Dashboard Porpouse |
    |===================================|
    */
    public function getMerchantDashboardData($input, $user_id)
    {
        $data = static::orderBy('id', 'DESC')
            ->where('user_id', $user_id);
        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no', 'like', '%' . $input['card_no'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->get();

        return $data;
    }
    public function getMerchantDashboardChartData($input, $user_id)
    {
        $successTran = \DB::table('transactions');

        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {

            $successTran = $successTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $successTran = $successTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }

        $successTran = $successTran->where('user_id', $user_id)
            ->where('status', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->where('transactions.is_retrieval', '0')
            ->whereNull('transactions.deleted_at')
            ->count();

        $failTran = \DB::table('transactions');

        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {

            $failTran = $failTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $failTran = $failTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }

        $failTran = $failTran->where('user_id', $user_id)
            ->where('status', '0')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->count();

        $pendingTran = \DB::table('transactions');

        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {

            $pendingTran = $pendingTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $pendingTran = $pendingTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }

        $pendingTran = $pendingTran->where('user_id', $user_id)
            ->where('status', '2')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->count();

        $toBeConfTran = \DB::table('transactions');

        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {

            $toBeConfTran = $toBeConfTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $toBeConfTran = $toBeConfTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }

        $toBeConfTran = $toBeConfTran->where('user_id', $user_id)
            ->where('status', '4')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->count();

        $canceledTran = \DB::table('transactions');

        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {

            $canceledTran = $canceledTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $canceledTran = $canceledTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 23:59:59');
        }

        $canceledTran = $canceledTran->where('user_id', $user_id)
            ->where('status', '3')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->count();

        return [
            'success' => $successTran,
            'fail' => $failTran,
            'pending' => $pendingTran,
            'tobeconf' => $toBeConfTran,
            'canceled' => $canceledTran,
        ];
    }

    public function getTransactionAmountByUserReportDaily($input)
    {
        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'CNY'];

        $mainData = [];
        foreach ($currencyArray as $key => $value) {


            $total_approve_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->whereDate('created_at', Carbon::today());

            $total_approve_transaction_count = $total_approve_transaction_amount->sum('TXs');
            $total_approve_transaction_amount1 = $total_approve_transaction_amount->sum('VOLs');

            $total_declined_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->whereDate('created_at', Carbon::today());

            $total_declined_transaction_count = $total_declined_transaction_amount->sum('TXd');
            $total_declined_transaction_amount1 = $total_declined_transaction_amount->sum('VOLd');

            $total_chargebacks_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->whereDate('created_at', Carbon::today());

            $total_chargebacks_transaction_count = $total_chargebacks_transaction_amount->sum('CBTX');
            $total_chargebacks_transaction_amount1 = $total_chargebacks_transaction_amount->sum('CBV');

            $total_refund_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->whereDate('created_at', Carbon::today());

            $total_refund_transaction_count = $total_refund_transaction_amount->sum('REFTX');
            $total_refund_transaction_amount1 = $total_refund_transaction_amount->sum('REFV');

            $total_flagged_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->whereDate('created_at', Carbon::today());

            $total_flagged_count = $total_flagged_amount->sum('FLGV');
            $total_flagged_amount1 = $total_flagged_amount->sum('FLGTX');

            if ($total_approve_transaction_amount1 != 0 || $total_approve_transaction_count != 0 || $total_declined_transaction_amount1 != 0 || $total_declined_transaction_count != 0 || $total_chargebacks_transaction_amount1 != 0 || $total_chargebacks_transaction_count != 0 || $total_refund_transaction_amount1 != 0 || $total_refund_transaction_count != 0 || $total_flagged_amount1 != 0 || $total_flagged_count != 0) {
                $mainData[$value] = [
                    'total_approve_transaction_amount' => $total_approve_transaction_amount1,
                    'total_approve_transaction_count' => $total_approve_transaction_count,
                    'total_declined_transaction_amount' => $total_declined_transaction_amount1,
                    'total_declined_transaction_count' => $total_declined_transaction_count,
                    'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount1,
                    'total_chargebacks_transaction_count' => $total_chargebacks_transaction_count,
                    'total_refund_transaction_amount' => $total_refund_transaction_amount1,
                    'total_refund_transaction_count' => $total_refund_transaction_count,
                    'total_flagged_amount' => $total_flagged_amount1,
                    'total_flagged_count' => $total_flagged_count,
                ];
            }
        }

        return $mainData;
    }

    public function getTransactionAmountByUserReportWeekly($input)
    {
        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'CNY'];

        $mainData = [];
        foreach ($currencyArray as $key => $value) {

            $fromDate = Carbon::now()->subDay()->startOfWeek()->toDateString();
            $tillDate = Carbon::now()->subDay()->endOfWeek()->toDateString();

            $total_approve_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_approve_transaction_count = $total_approve_transaction_amount->sum('TXs');
            $total_approve_transaction_amount1 = $total_approve_transaction_amount->sum('VOLs');

            $total_declined_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_declined_transaction_count = $total_declined_transaction_amount->sum('TXd');
            $total_declined_transaction_amount1 = $total_declined_transaction_amount->sum('VOLd');

            $total_chargebacks_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_chargebacks_transaction_count = $total_chargebacks_transaction_amount->sum('CBTX');
            $total_chargebacks_transaction_amount1 = $total_chargebacks_transaction_amount->sum('CBV');

            $total_refund_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_refund_transaction_count = $total_refund_transaction_amount->sum('REFTX');
            $total_refund_transaction_amount1 = $total_refund_transaction_amount->sum('REFV');

            $total_flagged_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_flagged_count = $total_flagged_amount->sum('FLGV');
            $total_flagged_amount1 = $total_flagged_amount->sum('FLGTX');

            if ($total_approve_transaction_amount1 != 0 || $total_approve_transaction_count != 0 || $total_declined_transaction_amount1 != 0 || $total_declined_transaction_count != 0 || $total_chargebacks_transaction_amount1 != 0 || $total_chargebacks_transaction_count != 0 || $total_refund_transaction_amount1 != 0 || $total_refund_transaction_count != 0 || $total_flagged_amount1 != 0 || $total_flagged_count != 0) {
                $mainData[$value] = [
                    'total_approve_transaction_amount' => $total_approve_transaction_amount1,
                    'total_approve_transaction_count' => $total_approve_transaction_count,
                    'total_declined_transaction_amount' => $total_declined_transaction_amount1,
                    'total_declined_transaction_count' => $total_declined_transaction_count,
                    'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount1,
                    'total_chargebacks_transaction_count' => $total_chargebacks_transaction_count,
                    'total_refund_transaction_amount' => $total_refund_transaction_amount1,
                    'total_refund_transaction_count' => $total_refund_transaction_count,
                    'total_flagged_amount' => $total_flagged_amount1,
                    'total_flagged_count' => $total_flagged_count,
                ];
            }
        }

        return $mainData;
    }

    public function getTransactionAmountByUserReportMonthly($input)
    {
        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'CNY'];

        $mainData = [];
        foreach ($currencyArray as $key => $value) {
            $fromDate = Carbon::now()->subDay()->startOfMonth()->toDateString();
            $tillDate = Carbon::now()->subDay()->endOfMonth()->toDateString();

            $total_approve_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_approve_transaction_count = $total_approve_transaction_amount->sum('TXs');
            $total_approve_transaction_amount1 = $total_approve_transaction_amount->sum('VOLs');

            $total_declined_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_declined_transaction_count = $total_declined_transaction_amount->sum('TXd');
            $total_declined_transaction_amount1 = $total_declined_transaction_amount->sum('VOLd');

            $total_chargebacks_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_chargebacks_transaction_count = $total_chargebacks_transaction_amount->sum('CBTX');
            $total_chargebacks_transaction_amount1 = $total_chargebacks_transaction_amount->sum('CBV');

            $total_refund_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_refund_transaction_count = $total_refund_transaction_amount->sum('REFTX');
            $total_refund_transaction_amount1 = $total_refund_transaction_amount->sum('REFV');

            $total_flagged_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_flagged_count = $total_flagged_amount->sum('FLGV');
            $total_flagged_amount1 = $total_flagged_amount->sum('FLGTX');

            if ($total_approve_transaction_amount1 != 0 || $total_approve_transaction_count != 0 || $total_declined_transaction_amount1 != 0 || $total_declined_transaction_count != 0 || $total_chargebacks_transaction_amount1 != 0 || $total_chargebacks_transaction_count != 0 || $total_refund_transaction_amount1 != 0 || $total_refund_transaction_count != 0 || $total_flagged_amount1 != 0 || $total_flagged_count != 0) {
                $mainData[$value] = [
                    'total_approve_transaction_amount' => $total_approve_transaction_amount1,
                    'total_approve_transaction_count' => $total_approve_transaction_count,
                    'total_declined_transaction_amount' => $total_declined_transaction_amount1,
                    'total_declined_transaction_count' => $total_declined_transaction_count,
                    'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount1,
                    'total_chargebacks_transaction_count' => $total_chargebacks_transaction_count,
                    'total_refund_transaction_amount' => $total_refund_transaction_amount1,
                    'total_refund_transaction_count' => $total_refund_transaction_count,
                    'total_flagged_amount' => $total_flagged_amount1,
                    'total_flagged_count' => $total_flagged_count,
                ];
            }
        }

        return $mainData;
    }
    /*
    |=============================|
    | For A Admin Porpouse        |
    |=============================|
    */

    public function getAdminChartData($input)
    {
        // $input['start_date'] = "01/01/2018";
        // $input['end_date']   = "12/31/2018";

        // Success transaction count
        /*$successTran = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {

            $successTran = $successTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $successTran = $successTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $successTran = $successTran->where('status', '1')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->where('transactions.is_retrieval', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->count();

        // Success transaction total amount
        $successTranAmount = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {

            $successTranAmount = $successTranAmount->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $successTranAmount = $successTranAmount->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $successTranAmount = $successTranAmount->where('status', '1')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->where('transactions.is_retrieval', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->sum('amount');

        // Declined transaction count
        $failTran = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $failTran = $failTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $failTran = $failTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $failTran = $failTran->where('status', '0')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->count();

        // Declined transaction total amount
        $failTranAmount = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $failTranAmount = $failTranAmount->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $failTranAmount = $failTranAmount->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $failTranAmount = $failTranAmount->where('status', '0')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->sum('amount');

        // Chargebacks transaction count
        $chargebacksTran = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $chargebacksTran = $chargebacksTran->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $chargebacksTran = $chargebacksTran->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $chargebacksTran = $chargebacksTran->where('chargebacks', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->count();

        // Chargebacks transaction total amount
        $chargebacksTranAmount = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $chargebacksTranAmount = $chargebacksTranAmount->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $chargebacksTranAmount = $chargebacksTranAmount->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $chargebacksTranAmount = $chargebacksTranAmount->where('chargebacks', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->sum('amount');

        // Refund transaction count
        $refundTran = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $refundTran = $refundTran->where(DB::raw('DATE(transactions.refund_date)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $refundTran = $refundTran->where(DB::raw('DATE(transactions.refund_date)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $refundTran = $refundTran->where('refund', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->count();

        // Refund transaction total amount
        $refundTranAmount = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $refundTranAmount = $refundTranAmount->where(DB::raw('DATE(transactions.refund_date)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $refundTranAmount = $refundTranAmount->where(DB::raw('DATE(transactions.refund_date)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $refundTranAmount = $refundTranAmount->where('refund', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->sum('amount');

        // Flagged transaction count
        $flaggedTran = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $flaggedTran = $flaggedTran->where(DB::raw('DATE(transactions.flagged_date)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $flaggedTran = $flaggedTran->where(DB::raw('DATE(transactions.flagged_date)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $flaggedTran = $flaggedTran->where('is_flagged', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->count();

        // Flagged transaction total amount
        $flaggedTranAmount = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $flaggedTranAmount = $flaggedTranAmount->where(DB::raw('DATE(transactions.flagged_date)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $flaggedTranAmount = $flaggedTranAmount->where(DB::raw('DATE(transactions.flagged_date)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $flaggedTranAmount = $flaggedTranAmount->where('is_flagged', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->sum('amount');

        // Pending transaction count
        $pendingTran = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $pendingTran = $pendingTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $pendingTran = $pendingTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $pendingTran = $pendingTran->where('status', '2')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->count();

        // Pending transaction total amount
        $pendingTranAmount = \DB::table('transactions');
        if(isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $pendingTranAmount = $pendingTranAmount->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d',strtotime($input['start_date'])).' 00:00:00');
            $pendingTranAmount = $pendingTranAmount->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d',strtotime($input['end_date'])).' 00:00:00');
        }
        $pendingTranAmount = $pendingTranAmount->where('status', '2')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->sum('amount');
        */
        // total transaction count and amount
        /*$totalTran = $successTran + $failTran + $chargebacksTran + $refundTran + $flaggedTran + $pendingTran;
        $totalTranAmount = $successTranAmount + $failTranAmount + $chargebacksTranAmount + $refundTranAmount + $flaggedTranAmount + $pendingTranAmount;*/


        $date_condition = "";
        $user_condition = "";

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $date_condition = "and created_at between '" . $start_date . "' and '" . $end_date . "' ";
        }
        // else {

        //     $date_condition = "and created_at between date_sub(now() , interval 31 day) and now() ";
        // }

        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $user_id = $input['user_id'];
            $user_condition = "and user_id = $user_id";
        }
        $table = '';

        $query = <<<SQL
    select  sum(volume) volume, sum(tx) as tx
    from

SQL;

        $where = <<<SQL
    where 1
    $user_condition
    $date_condition
SQL;

        $table = 'tx_success';
        $select = $query . $table . $where;
        $successD = \DB::select($select)[0];
        $successD = (array) $successD;

        $table = 'tx_decline';
        $select = $query . $table . $where;
        $failD = \DB::select($select)[0];
        $failD = (array) $failD;

        $table = 'tx_chargebacks';
        $select = $query . $table . $where;
        $chargebacksD = \DB::select($select)[0];
        $chargebacksD = (array) $chargebacksD;

        $table = 'tx_refunds';
        $select = $query . $table . $where;
        $refundD = \DB::select($select)[0];
        $refundD = (array) $refundD;

        $table = 'tx_flagged';
        $select = $query . $table . $where;
        $flaggdD = \DB::select($select)[0];
        $flaggdD = (array) $flaggdD;

        $table = 'tx_pending';
        $select = $query . $table . $where;
        $pendingD = \DB::select($select)[0];
        $pendingD = (array) $pendingD;


        $successTran = $successD['tx'];
        $failTran = $failD['tx'];
        $chargebacksTran = $chargebacksD['tx'];
        $refundTran = $refundD['tx'];
        $flaggedTran = $flaggdD['tx'];
        $pendingTran = $pendingD['tx'];
        $totalTran = $successTran
            + $failTran
            + $chargebacksTran
            + $refundTran
            + $flaggedTran
            + $pendingTran;

        $successTranAmount = $successD['volume'];
        $failTranAmount = $failD['volume'];
        $chargebacksTranAmount = $chargebacksD['volume'];
        $refundTranAmount = $refundD['volume'];
        $flaggedTranAmount = $flaggdD['volume'];
        $pendingTranAmount = $pendingD['volume'];
        $totalTranAmount = $successTran
            + $failTranAmount
            + $chargebacksTranAmount
            + $refundTranAmount
            + $flaggedTranAmount
            + $pendingTranAmount;



        return [
            'success' => $successTran,
            'fail' => $failTran,
            'chargebacks' => $chargebacksTran,
            'refund' => $refundTran,
            'flagged' => $flaggedTran,
            'pending' => $pendingTran,
            'total' => $totalTran,
            'successamount' => $successTranAmount,
            'failamount' => $failTranAmount,
            'chargebacksamount' => $chargebacksTranAmount,
            'refundamount' => $refundTranAmount,
            'flaggedamount' => $flaggedTranAmount,
            'pendingamount' => $pendingTranAmount,
            'totalamount' => $totalTranAmount,
        ];
    }

    public function getUserTotalAmount($user_id, $currency, $input)
    {
        // Success transaction count
        $successTran = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {

            $successTran = $successTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $successTran = $successTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 00:00:00');
        }
        $successTran = $successTran->where('status', '1')
            ->where('user_id', $user_id)
            ->where('currency', $currency)
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->where('transactions.is_retrieval', '0')
            ->whereNull('transactions.deleted_at')
            ->count();

        // Success transaction total amount
        $successTranAmount = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {

            $successTranAmount = $successTranAmount->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $successTranAmount = $successTranAmount->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 00:00:00');
        }
        $successTranAmount = $successTranAmount->where('status', '1')
            ->where('user_id', $user_id)
            ->where('currency', $currency)
            ->where('chargebacks', '<>', '1')
            ->where('payment_gateway_id', '<>', '16')
            ->where('payment_gateway_id', '<>', '41')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->where('transactions.is_retrieval', '0')
            ->whereNull('transactions.deleted_at')
            ->sum('amount');

        // Declined transaction count
        $failTran = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $failTran = $failTran->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $failTran = $failTran->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 00:00:00');
        }
        $failTran = $failTran->where('status', '0')
            ->where('user_id', $user_id)
            ->where('currency', $currency)
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->count();

        // Declined transaction total amount
        $failTranAmount = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $failTranAmount = $failTranAmount->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $failTranAmount = $failTranAmount->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 00:00:00');
        }
        $failTranAmount = $failTranAmount->where('status', '0')
            ->where('user_id', $user_id)
            ->where('currency', $currency)
            ->where('chargebacks', '<>', '1')
            ->where('payment_gateway_id', '<>', '16')
            ->where('payment_gateway_id', '<>', '41')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->sum('amount');

        // Chargebacks transaction count
        $chargebacksTran = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $chargebacksTran = $chargebacksTran->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $chargebacksTran = $chargebacksTran->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 00:00:00');
        }
        $chargebacksTran = $chargebacksTran->where('chargebacks', '1')
            ->where('user_id', $user_id)
            ->where('currency', $currency)
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->count();

        // Chargebacks transaction total amount
        $chargebacksTranAmount = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $chargebacksTranAmount = $chargebacksTranAmount->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $chargebacksTranAmount = $chargebacksTranAmount->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 00:00:00');
        }
        $chargebacksTranAmount = $chargebacksTranAmount->where('chargebacks', '1')
            ->where('user_id', $user_id)
            ->where('currency', $currency)
            ->where('payment_gateway_id', '<>', '16')
            ->where('payment_gateway_id', '<>', '41')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->sum('amount');

        // Refund transaction count
        $refundTran = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $refundTran = $refundTran->where(DB::raw('DATE(transactions.refund_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $refundTran = $refundTran->where(DB::raw('DATE(transactions.refund_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 00:00:00');
        }
        $refundTran = $refundTran->where('refund', '1')
            ->where('user_id', $user_id)
            ->where('currency', $currency)
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->count();

        // Refund transaction total amount
        $refundTranAmount = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $refundTranAmount = $refundTranAmount->where(DB::raw('DATE(transactions.refund_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $refundTranAmount = $refundTranAmount->where(DB::raw('DATE(transactions.refund_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 00:00:00');
        }
        $refundTranAmount = $refundTranAmount->where('refund', '1')
            ->where('user_id', $user_id)
            ->where('currency', $currency)
            ->where('payment_gateway_id', '<>', '16')
            ->where('payment_gateway_id', '<>', '41')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->sum('amount');

        // Flagged transaction count
        $flaggedTran = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $flaggedTran = $flaggedTran->where(DB::raw('DATE(transactions.flagged_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $flaggedTran = $flaggedTran->where(DB::raw('DATE(transactions.flagged_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 00:00:00');
        }
        $flaggedTran = $flaggedTran->where('is_flagged', '1')
            ->where('user_id', $user_id)
            ->where('currency', $currency)
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->count();

        // Flagged transaction total amount
        $flaggedTranAmount = \DB::table('transactions');
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $flaggedTranAmount = $flaggedTranAmount->where(DB::raw('DATE(transactions.flagged_date)'), '>=', date('Y-m-d', strtotime($input['start_date'])) . ' 00:00:00');
            $flaggedTranAmount = $flaggedTranAmount->where(DB::raw('DATE(transactions.flagged_date)'), '<=', date('Y-m-d', strtotime($input['end_date'])) . ' 00:00:00');
        }
        $flaggedTranAmount = $flaggedTranAmount->where('is_flagged', '1')
            ->where('user_id', $user_id)
            ->where('currency', $currency)
            ->where('payment_gateway_id', '<>', '16')
            ->where('payment_gateway_id', '<>', '41')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->sum('amount');

        return [
            'success' => $successTran,
            'fail' => $failTran,
            'chargebacks' => $chargebacksTran,
            'refund' => $refundTran,
            'flagged' => $flaggedTran,
            'successamount' => $successTranAmount,
            'failamount' => $failTranAmount,
            'chargebacksamount' => $chargebacksTranAmount,
            'refundamount' => $refundTranAmount,
            'flaggedamount' => $flaggedTranAmount,
        ];
    }

    public function getTransactionAmountReport($input)
    {
        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'CNY'];

        $mainData = [];
        foreach ($currencyArray as $key => $value) {
            // Check Transaction in currency
            $chekTransactionInCurrency = static::where('payment_gateway_id', '<>', '16');
            if (isset($input['company_name']) && $input['company_name'] != '') {
                $chekTransactionInCurrency = $chekTransactionInCurrency->where('user_id', $input['company_name']);
            }
            if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                $chekTransactionInCurrency = $chekTransactionInCurrency->where('payment_gateway_id', $input['payment_gateway_id']);
            }
            $chekTransactionInCurrency = $chekTransactionInCurrency->where('currency', $value)
                ->count();

            if ($chekTransactionInCurrency > 0) {
                $total_approve_transaction_amount = static::where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_retrieval', '0')
                    ->where('currency', $value)
                    ->where('status', '1');
                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $start_date = date('Y-m-d', strtotime($input['start_date']));
                    $end_date = date('Y-m-d', strtotime($input['end_date']));

                    $total_approve_transaction_amount = $total_approve_transaction_amount->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                        ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 00:00:00');
                }
                if (isset($input['company_name']) && $input['company_name'] != '') {
                    $total_approve_transaction_amount = $total_approve_transaction_amount->where('user_id', $input['company_name']);
                }
                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $total_approve_transaction_amount = $total_approve_transaction_amount->where('payment_gateway_id', $input['payment_gateway_id']);
                }
                $total_approve_transaction_amount = $total_approve_transaction_amount->sum('amount');

                $total_declined_transaction_amount = static::where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('chargebacks', '<>', '1')
                    ->where('refund', '<>', '1')
                    ->where('currency', $value)
                    ->where('status', '0');
                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $start_date = date('Y-m-d', strtotime($input['start_date']));
                    $end_date = date('Y-m-d', strtotime($input['end_date']));

                    $total_declined_transaction_amount = $total_declined_transaction_amount->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                        ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 00:00:00');
                }
                if (isset($input['company_name']) && $input['company_name'] != '') {
                    $total_declined_transaction_amount = $total_declined_transaction_amount->where('user_id', $input['company_name']);
                }
                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $total_declined_transaction_amount = $total_declined_transaction_amount->where('payment_gateway_id', $input['payment_gateway_id']);
                }
                $total_declined_transaction_amount = $total_declined_transaction_amount->sum('amount');

                $total_chargebacks_transaction_amount = static::where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $value)
                    ->where('chargebacks', '1');
                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $start_date = date('Y-m-d', strtotime($input['start_date']));
                    $end_date = date('Y-m-d', strtotime($input['end_date']));

                    $total_chargebacks_transaction_amount = $total_chargebacks_transaction_amount->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                        ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 00:00:00');
                }
                if (isset($input['company_name']) && $input['company_name'] != '') {
                    $total_chargebacks_transaction_amount = $total_chargebacks_transaction_amount->where('user_id', $input['company_name']);
                }
                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $total_chargebacks_transaction_amount = $total_chargebacks_transaction_amount->where('payment_gateway_id', $input['payment_gateway_id']);
                }
                $total_chargebacks_transaction_amount = $total_chargebacks_transaction_amount->sum('amount');

                $total_refund_transaction_amount = static::where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $value)
                    ->where('refund', '1');
                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $start_date = date('Y-m-d', strtotime($input['start_date']));
                    $end_date = date('Y-m-d', strtotime($input['end_date']));

                    $total_refund_transaction_amount = $total_refund_transaction_amount->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                        ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 00:00:00');
                }
                if (isset($input['company_name']) && $input['company_name'] != '') {
                    $total_refund_transaction_amount = $total_refund_transaction_amount->where('user_id', $input['company_name']);
                }
                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $total_refund_transaction_amount = $total_refund_transaction_amount->where('payment_gateway_id', $input['payment_gateway_id']);
                }
                $total_refund_transaction_amount = $total_refund_transaction_amount->sum('amount');

                $total_flagged_amount = static::where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $value)
                    ->where('is_flagged', '1');
                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $start_date = date('Y-m-d', strtotime($input['start_date']));
                    $end_date = date('Y-m-d', strtotime($input['end_date']));

                    $total_flagged_amount = $total_flagged_amount->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                        ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 00:00:00');
                }
                if (isset($input['company_name']) && $input['company_name'] != '') {
                    $total_flagged_amount = $total_flagged_amount->where('user_id', $input['company_name']);
                }
                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $total_flagged_amount = $total_flagged_amount->where('payment_gateway_id', $input['payment_gateway_id']);
                }
                $total_flagged_amount = $total_flagged_amount->sum('amount');

                $mainData[$value] = [
                    'total_approve_transaction_amount' => $total_approve_transaction_amount,
                    'total_declined_transaction_amount' => $total_declined_transaction_amount,
                    'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount,
                    'total_refund_transaction_amount' => $total_refund_transaction_amount,
                    'total_flagged_amount' => $total_flagged_amount,
                ];
            }
        }

        return $mainData;
    }

    public function getTransactionAmountReportDaily($input)
    {
        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'CNY'];

        $mainData = [];
        foreach ($currencyArray as $key => $value) {


            $total_approve_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->whereDate('created_at', Carbon::today());

            $total_approve_transaction_count = $total_approve_transaction_amount->sum('TXs');
            $total_approve_transaction_amount1 = $total_approve_transaction_amount->sum('VOLs');

            $total_declined_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->whereDate('created_at', Carbon::today());

            $total_declined_transaction_count = $total_declined_transaction_amount->sum('TXd');
            $total_declined_transaction_amount1 = $total_declined_transaction_amount->sum('VOLd');

            $total_chargebacks_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->whereDate('created_at', Carbon::today());

            $total_chargebacks_transaction_count = $total_chargebacks_transaction_amount->sum('CBTX');
            $total_chargebacks_transaction_amount1 = $total_chargebacks_transaction_amount->sum('CBV');

            $total_refund_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->whereDate('created_at', Carbon::today());

            $total_refund_transaction_count = $total_refund_transaction_amount->sum('REFTX');
            $total_refund_transaction_amount1 = $total_refund_transaction_amount->sum('REFV');

            $total_flagged_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->whereDate('created_at', Carbon::today());

            $total_flagged_count = $total_flagged_amount->sum('FLGV');
            $total_flagged_amount1 = $total_flagged_amount->sum('FLGTX');

            if ($total_approve_transaction_amount1 != 0 || $total_approve_transaction_count != 0 || $total_declined_transaction_amount1 != 0 || $total_declined_transaction_count != 0 || $total_chargebacks_transaction_amount1 != 0 || $total_chargebacks_transaction_count != 0 || $total_refund_transaction_amount1 != 0 || $total_refund_transaction_count != 0 || $total_flagged_amount1 != 0 || $total_flagged_count != 0) {
                $mainData[$value] = [
                    'total_approve_transaction_amount' => $total_approve_transaction_amount1,
                    'total_approve_transaction_count' => $total_approve_transaction_count,
                    'total_declined_transaction_amount' => $total_declined_transaction_amount1,
                    'total_declined_transaction_count' => $total_declined_transaction_count,
                    'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount1,
                    'total_chargebacks_transaction_count' => $total_chargebacks_transaction_count,
                    'total_refund_transaction_amount' => $total_refund_transaction_amount1,
                    'total_refund_transaction_count' => $total_refund_transaction_count,
                    'total_flagged_amount' => $total_flagged_amount1,
                    'total_flagged_count' => $total_flagged_count,
                ];
            }
        }

        return $mainData;
    }

    public function getTransactionAmountReportWeekly($input)
    {
        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'CNY'];

        $mainData = [];
        foreach ($currencyArray as $key => $value) {

            $fromDate = Carbon::now()->subDay()->startOfWeek()->toDateString();
            $tillDate = Carbon::now()->subDay()->endOfWeek()->toDateString();

            $total_approve_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_approve_transaction_count = $total_approve_transaction_amount->sum('TXs');
            $total_approve_transaction_amount1 = $total_approve_transaction_amount->sum('VOLs');

            $total_declined_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_declined_transaction_count = $total_declined_transaction_amount->sum('TXd');
            $total_declined_transaction_amount1 = $total_declined_transaction_amount->sum('VOLd');

            $total_chargebacks_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_chargebacks_transaction_count = $total_chargebacks_transaction_amount->sum('CBTX');
            $total_chargebacks_transaction_amount1 = $total_chargebacks_transaction_amount->sum('CBV');

            $total_refund_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_refund_transaction_count = $total_refund_transaction_amount->sum('REFTX');
            $total_refund_transaction_amount1 = $total_refund_transaction_amount->sum('REFV');

            $total_flagged_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_flagged_count = $total_flagged_amount->sum('FLGV');
            $total_flagged_amount1 = $total_flagged_amount->sum('FLGTX');

            if ($total_approve_transaction_amount1 != 0 || $total_approve_transaction_count != 0 || $total_declined_transaction_amount1 != 0 || $total_declined_transaction_count != 0 || $total_chargebacks_transaction_amount1 != 0 || $total_chargebacks_transaction_count != 0 || $total_refund_transaction_amount1 != 0 || $total_refund_transaction_count != 0 || $total_flagged_amount1 != 0 || $total_flagged_count != 0) {
                $mainData[$value] = [
                    'total_approve_transaction_amount' => $total_approve_transaction_amount1,
                    'total_approve_transaction_count' => $total_approve_transaction_count,
                    'total_declined_transaction_amount' => $total_declined_transaction_amount1,
                    'total_declined_transaction_count' => $total_declined_transaction_count,
                    'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount1,
                    'total_chargebacks_transaction_count' => $total_chargebacks_transaction_count,
                    'total_refund_transaction_amount' => $total_refund_transaction_amount1,
                    'total_refund_transaction_count' => $total_refund_transaction_count,
                    'total_flagged_amount' => $total_flagged_amount1,
                    'total_flagged_count' => $total_flagged_count,
                ];
            }
        }

        return $mainData;
    }

    public function getTransactionAmountReportMonthly($input)
    {
        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'CNY'];

        $mainData = [];
        foreach ($currencyArray as $key => $value) {
            $fromDate = Carbon::now()->subDay()->startOfMonth()->toDateString();
            $tillDate = Carbon::now()->subDay()->endOfMonth()->toDateString();

            $total_approve_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_approve_transaction_count = $total_approve_transaction_amount->sum('TXs');
            $total_approve_transaction_amount1 = $total_approve_transaction_amount->sum('VOLs');

            $total_declined_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_declined_transaction_count = $total_declined_transaction_amount->sum('TXd');
            $total_declined_transaction_amount1 = $total_declined_transaction_amount->sum('VOLd');

            $total_chargebacks_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_chargebacks_transaction_count = $total_chargebacks_transaction_amount->sum('CBTX');
            $total_chargebacks_transaction_amount1 = $total_chargebacks_transaction_amount->sum('CBV');

            $total_refund_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_refund_transaction_count = $total_refund_transaction_amount->sum('REFTX');
            $total_refund_transaction_amount1 = $total_refund_transaction_amount->sum('REFV');

            $total_flagged_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where(\DB::raw('DATE(created_at)'), '>=', $fromDate . ' 00:00:00')
                ->where(\DB::raw('DATE(created_at)'), '<=', $tillDate . ' 23:59:59');

            $total_flagged_count = $total_flagged_amount->sum('FLGV');
            $total_flagged_amount1 = $total_flagged_amount->sum('FLGTX');

            if ($total_approve_transaction_amount1 != 0 || $total_approve_transaction_count != 0 || $total_declined_transaction_amount1 != 0 || $total_declined_transaction_count != 0 || $total_chargebacks_transaction_amount1 != 0 || $total_chargebacks_transaction_count != 0 || $total_refund_transaction_amount1 != 0 || $total_refund_transaction_count != 0 || $total_flagged_amount1 != 0 || $total_flagged_count != 0) {
                $mainData[$value] = [
                    'total_approve_transaction_amount' => $total_approve_transaction_amount1,
                    'total_approve_transaction_count' => $total_approve_transaction_count,
                    'total_declined_transaction_amount' => $total_declined_transaction_amount1,
                    'total_declined_transaction_count' => $total_declined_transaction_count,
                    'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount1,
                    'total_chargebacks_transaction_count' => $total_chargebacks_transaction_count,
                    'total_refund_transaction_amount' => $total_refund_transaction_amount1,
                    'total_refund_transaction_count' => $total_refund_transaction_count,
                    'total_flagged_amount' => $total_flagged_amount1,
                    'total_flagged_count' => $total_flagged_count,
                ];
            }
        }

        return $mainData;
    }
    public function getTransactionSummaryReport($input)
    {
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $start_date = $start_date . " 00:00:00";
            $end_date = $end_date . " 23:59:59";
        }

        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'CNY'];

        $mainData = [];
        foreach ($currencyArray as $key => $value) {

            $total_approve_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id);

            if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                $total_approve_transaction_amount = $total_approve_transaction_amount
                    ->where(\DB::raw('DATE(created_at)'), '>=', $start_date . ' 00:00:00')
                    ->where(\DB::raw('DATE(created_at)'), '<=', $end_date . ' 23:59:59');
            }

            $total_approve_transaction_count = $total_approve_transaction_amount->sum('TXs');
            $total_approve_transaction_amount1 = $total_approve_transaction_amount->sum('VOLs');

            $total_declined_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id);

            if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                $total_declined_transaction_amount = $total_declined_transaction_amount
                    ->where(\DB::raw('DATE(created_at)'), '>=', $start_date . ' 00:00:00')
                    ->where(\DB::raw('DATE(created_at)'), '<=', $end_date . ' 23:59:59');
            }

            $total_declined_transaction_count = $total_declined_transaction_amount->sum('TXd');
            $total_declined_transaction_amount1 = $total_declined_transaction_amount->sum('VOLd');

            $total_chargebacks_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id);

            if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                $total_chargebacks_transaction_amount = $total_chargebacks_transaction_amount
                    ->where(\DB::raw('DATE(created_at)'), '>=', $start_date . ' 00:00:00')
                    ->where(\DB::raw('DATE(created_at)'), '<=', $end_date . ' 23:59:59');
            }

            $total_chargebacks_transaction_count = $total_chargebacks_transaction_amount->sum('CBTX');
            $total_chargebacks_transaction_amount1 = $total_chargebacks_transaction_amount->sum('CBV');

            $total_refund_transaction_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id);

            if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                $total_refund_transaction_amount = $total_refund_transaction_amount
                    ->where(\DB::raw('DATE(created_at)'), '>=', $start_date . ' 00:00:00')
                    ->where(\DB::raw('DATE(created_at)'), '<=', $end_date . ' 23:59:59');
            }

            $total_refund_transaction_count = $total_refund_transaction_amount->sum('REFTX');
            $total_refund_transaction_amount1 = $total_refund_transaction_amount->sum('REFV');

            $total_flagged_amount = \DB::table('tx_payout')
                ->where('currency', $value)
                ->where('user_id', Auth::user()->id);

            if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                $total_flagged_amount = $total_flagged_amount
                    ->where(\DB::raw('DATE(created_at)'), '>=', $start_date . ' 00:00:00')
                    ->where(\DB::raw('DATE(created_at)'), '<=', $end_date . ' 23:59:59');
            }

            $total_flagged_count = $total_flagged_amount->sum('FLGTX');
            $total_flagged_amount1 = $total_flagged_amount->sum('FLGV');

            if ($total_approve_transaction_amount1 != 0 || $total_approve_transaction_count != 0 || $total_declined_transaction_amount1 != 0 || $total_declined_transaction_count != 0 || $total_chargebacks_transaction_amount1 != 0 || $total_chargebacks_transaction_count != 0 || $total_refund_transaction_amount1 != 0 || $total_refund_transaction_count != 0 || $total_flagged_amount1 != 0 || $total_flagged_count != 0) {
                $mainData[$value] = [
                    'total_approve_transaction_amount' => $total_approve_transaction_amount1,
                    'total_approve_transaction_count' => $total_approve_transaction_count,
                    'total_declined_transaction_amount' => $total_declined_transaction_amount1,
                    'total_declined_transaction_count' => $total_declined_transaction_count,
                    'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount1,
                    'total_chargebacks_transaction_count' => $total_chargebacks_transaction_count,
                    'total_refund_transaction_amount' => $total_refund_transaction_amount1,
                    'total_refund_transaction_count' => $total_refund_transaction_count,
                    'total_flagged_amount' => $total_flagged_amount1,
                    'total_flagged_count' => $total_flagged_count,
                ];
            }
        }

        return $mainData;
    }

    public function getAllMerchantTransactionData($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select("order_id", "id", "email", "amount", "is_request_from_vt", "payment_type", "card_type", "is_white_label", "request_origin", "request_from_ip", "payment_gateway_id", "status", "amount", "created_at", "currency", "is_converted", "converted_currency", "converted_amount", "country", "user_id",'chargebacks','is_flagged','is_retrieval','refund')
            ->whereNotIn('payment_gateway_id', $payment_gateway_id);
        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }

        // ->where('transactions.payment_gateway_id', '!=', '1'
        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }
        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no', 'like', '%' . $input['card_no'] . '%');
        }
        if (isset($input['amount']) && $input['amount'] != '') {
            $data = $data->where('transactions.amount', '>=', $input['amount']);
        }
        
        if (isset($input['transaction_ref']) && $input['transaction_ref'] != '') {
            $data = $data->where("transactions.customer_order_id", '=', $input['transaction_ref']);
        }
        if (isset($input['greater_then']) && $input['greater_then'] != '') {
            $data = $data->where('transactions.amount', '>=', $input['greater_then']);
        }
        if (isset($input['less_then']) && $input['less_then'] != '') {
            $data = $data->where('transactions.amount', '<=', $input['less_then']);
        }
        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }
        if (isset($input['session_id']) && $input['session_id'] != '') {
            $data = $data->where('transactions.session_id', 'like', '%' . $input['session_id'] . '%');
        }
        if (isset($input['gateway_id']) && $input['gateway_id'] != '') {
            $data = $data->where('transactions.gateway_id', $input['gateway_id']);
        }
        if (isset($input['is_request_from_vt']) && $input['is_request_from_vt'] != '') {
            if ($input['is_request_from_vt'] == 'iFrame') {
                $data = $data->where('transactions.is_request_from_vt', $input['is_request_from_vt']);
            }

            if ($input['is_request_from_vt'] == 'Pay Button') {
                $data = $data->where('transactions.is_request_from_vt', $input['is_request_from_vt']);
            }

            if ($input['is_request_from_vt'] == 'WEBHOOK') {
                $data = $data->where('transactions.is_request_from_vt', $input['is_request_from_vt']);
            }

            if ($input['is_request_from_vt'] == 'API V2') {
                $data = $data->where('transactions.is_request_from_vt', $input['is_request_from_vt']);
            }

            if ($input['is_request_from_vt'] == 'API') {
                $data = $data->where(function ($query) use ($input) {
                    $query->where('transactions.is_request_from_vt', $input['is_request_from_vt'])
                        ->orWhere('transactions.is_request_from_vt', '0');
                });
            }
        }
        if (isset($input['reason']) && $input['reason'] != '') {
            $data = $data->where('transactions.reason', 'like', '%' . $input['reason'] . '%');
        }
        if (isset($input['is_white_label']) && $input['is_white_label'] != '') {
            $data = $data->where("transactions.is_white_label", '=', $input['is_white_label']);
        }
        $this->filterTransactionData($input, $data);
        $data = $data->orderBy('id', 'desc')->paginate($noList);

        return $data;
    }

    public function getAllMerchantCryptoTransactionData($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select("transactions.*", "applications.business_name as userName", "middetails.bank_name")
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->whereNotIn('payment_gateway_id', $payment_gateway_id)
            // ->where('transactions.payment_gateway_id', '!=', '1')
            ->where('transactions.is_transaction_type', 'CRYPTO');
        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }
        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }
        if (isset($input['amount']) && $input['amount'] != '') {
            $data = $data->where('transactions.amount', '>=', $input['amount']);
        }
        if (isset($input['greater_then']) && $input['greater_then'] != '') {
            $data = $data->where('transactions.amount', '>=', $input['greater_then']);
        }
        if (isset($input['less_then']) && $input['less_then'] != '') {
            $data = $data->where('transactions.amount', '<=', $input['less_then']);
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }
        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }
        if (isset($input['customer_order_id']) && $input['customer_order_id'] != '') {
            $data = $data->where('transactions.customer_order_id', 'like', '%' . $input['customer_order_id'] . '%');
        }
        if (isset($input['session_id']) && $input['session_id'] != '') {
            $data = $data->where('transactions.session_id', 'like', '%' . $input['session_id'] . '%');
        }
        if (isset($input['gateway_id']) && $input['gateway_id'] != '') {
            $data = $data->where('transactions.gateway_id', $input['gateway_id']);
        }
        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }
        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no', 'like', '%' . $input['card_no'] . '%');
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }
        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }
        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }
        if (isset($input['reason']) && $input['reason'] != '') {
            $data = $data->where('transactions.reason', 'like', '%' . $input['reason'] . '%');
        }
        if (isset($input['is_request_from_vt']) && $input['is_request_from_vt'] != '') {
            if ($input['is_request_from_vt'] == 'iFrame') {
                $data = $data->where('transactions.is_request_from_vt', $input['is_request_from_vt']);
            }

            if ($input['is_request_from_vt'] == 'Pay Button') {
                $data = $data->where('transactions.is_request_from_vt', $input['is_request_from_vt']);
            }

            if ($input['is_request_from_vt'] == 'WEBHOOK') {
                $data = $data->where('transactions.is_request_from_vt', $input['is_request_from_vt']);
            }

            if ($input['is_request_from_vt'] == 'API') {
                $data = $data->where(function ($query) use ($input) {
                    $query->where('transactions.is_request_from_vt', $input['is_request_from_vt'])
                        ->orWhere('transactions.is_request_from_vt', '0');
                });
            }
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = $input['end_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        $data = $data->orderBy('id', 'desc')->paginate($noList);
        return $data;
    }

    public function getSubTransactionData($input, $noList, $id)
    {
        $data = static::select('applications.business_name', 'transactions.*', 'middetails.bank_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->where('transactions.is_reccuring_transaction_id', $id)
            ->orderBy('id', 'DESC');

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['amount']) && $input['amount'] != '') {
            $data = $data->where('transactions.amount', '>=', $input['amount']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }

        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no', 'like', '%' . $input['card_no'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }

        if (isset($input['type']) && $input['type'] == 'xlsx') {
            $data = $data->get();
        } else {
            $data = $data->paginate($noList);
        }
        return $data;
    }

    public function getAllMerchantRecurringTransactionData($input, $noList)
    {
        $data = static::select('applications.business_name', 'transactions.*', 'middetails.bank_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->orderBy('id', 'DESC');

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['amount']) && $input['amount'] != '') {
            $data = $data->where('transactions.amount', '>=', $input['amount']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }

        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name', 'like', '%' . $input['company_name'] . '%');
        }

        // if(isset($input['card_no']) && $input['card_no'] != '') {
        //     $data = $data->where('transactions.card_no',  'like', '%' . $input['card_no'] . '%');
        // }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }

        $data = $data->where('transactions.is_recurring', '1');

        if (isset($input['type']) && $input['type'] == 'xlsx') {
            $data = $data->get();
        } else {
            if (isset($input['card_no']) && $input['card_no'] != '') {
                $filteredTransactions = $data->get()->filter(function ($record) use ($input) {
                    if (strpos($record->card_no, $input['card_no']) !== false) {
                        return $record;
                    }
                });
                $perPage = $noList;
                $currentPage = (!empty($input['page']) ? $input['page'] : 1);
                $pagedData = $filteredTransactions->slice(($currentPage - 1) * $perPage, $perPage)->all();
                $data = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($filteredTransactions), $perPage, $currentPage, ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]);
            } else {
                $data = $data->paginate($noList);
            }
        }
        return $data;
    }

    public function changeChargebacksStatus($id, $status)
    {
        return static::find($id)->update(['chargebacks' => $status]);
    }

    public function changeRefundStatus($id, $status)
    {
        return static::find($id)->update(['refund' => $status]);
    }

    public function getAllFlaggedTransactionData($input)
    {
        $data = static::select('applications.business_name', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"flagged"'));
            });

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }

        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no', 'like', '%' . $input['card_no'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.flagged_date)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('transactions.is_flagged', '1')
            ->orderBy('transactions.flagged_date', 'desc');

        $data = $data->paginate($input['paginate']);

        return $data;
    }

    public function getAllChargebackTransactionData($input)
    {
        $data = static::select('applications.business_name', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"chargebacks"'));
            });

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }

        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no', 'like', '%' . $input['card_no'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', $end_date . ' 23:59:59');
        }

        $data = $data->where('transactions.chargebacks', '1')
            ->orderBy('transactions.chargebacks_date', 'desc');

        return $data->paginate($input['paginate']);
    }

    public function getAllRefundTransactionsData($input)
    {
        $data = static::select('applications.business_name', 'transactions.*')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->orderBy('refund_date', 'desc');
        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }

        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name', 'like', '%' . $input['company_name'] . '%');
        }

        // if(isset($input['card_no']) && $input['card_no'] != '') {
        //     $data = $data->where('transactions.card_no',  'like', '%' . $input['card_no'] . '%');
        // }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.refund_date)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('transactions.refund', '1')
            ->orderBy('transactions.refund_date', 'desc');

        // if(isset($input['card_no']) && $input['card_no'] != '') {
        //     $data = $data->get()->filter(function($record) use($input) {
        //         if(strpos($record->card_no, $input['card_no']) !== false ) {
        //             return $record;
        //         }
        //     });
        // } else {
        // }
        $data = $data->paginate($input['paginate']);

        return $data;
    }

    public function getAllMerchantTestTransactionData($input, $noList)
    {
        $data = static::select('applications.business_name', 'transactions.*')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->orderBy('id', 'DESC');

        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.refund_date)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where(function ($query) {
            $query->orWhere('transactions.payment_gateway_id', '16')
                ->orWhere('transactions.payment_gateway_id', '41');
        });

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $filteredTransactions = $data->get()->filter(function ($record) use ($input) {
                if (strpos($record->card_no, $input['card_no']) !== false) {
                    return $record;
                }
            });
            $perPage = 10;
            $currentPage = (!empty($input['page']) ? $input['page'] : 1);
            $pagedData = $filteredTransactions->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $data = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($filteredTransactions), $perPage, $currentPage, ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]);
        } else {
            // $data = $data->paginate(10);
            if (isset($input['type']) && $input['type'] == 'xlsx') {
                if (isset($input['search_type']) && $input['search_type'] == 'resubmit') {
                    if (isset($input['ids']) && $input['ids'] != '') {
                        $ids = explode(',', $input['ids']);
                        $data = $data->whereIn('transactions.id', $ids);
                    }
                    // $data = $data->where('batch_transaction_counter', 0)
                    $data = $data->groupBy('transactions.email');
                }
                $data = $data->get();
            } else {
                $data = $data->paginate($noList);
            }
        }

        return $data;
    }

    public function getAllBatchTransactionData($input)
    {
        // dd($input);
        $data = static::select('transactions.*', 'middetails.bank_name')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->where('transactions.is_batch_transaction', '1')
            ->orderBy('id', 'DESC');

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }
        if (isset($input['amount']) && $input['amount'] != '') {
            $data = $data->where('transactions.amount', '>=', $input['amount']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }

        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no', 'like', '%' . $input['card_no'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->get();
        return $data;
    }

    public function getAllUserRetrieval($user_id, $input)
    {
        $data = static::select('transactions.*', 'transactions_document_upload.files as transactions_document_upload_files')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"retrieval"'));
            })
            ->where('transactions.user_id', $user_id)
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.retrieval_date)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.retrieval_date)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('is_retrieval', '1');


        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->get()->filter(function ($record) use ($input) {
                if (strpos($record->card_no, $input['card_no']) !== false) {
                    return $record;
                }
            });
        } else {
            $data = $data->get();
        }

        return $data;
    }

    public function getAllRetrievalTransactionData($input)
    {
        $data = static::select('applications.business_name', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"retrieval"'));
            });

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }

        if (isset($input['card_type']) && $input['card_type'] != '') {
            $data = $data->where('transactions.card_type', $input['card_type']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['card_no']) && $input['card_no'] != '') {
            $data = $data->where('transactions.card_no', 'like', '%' . $input['card_no'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.retrieval_date)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.retrieval_date)'), '<=', $end_date . ' 23:59:59');
        }
        $data = $data->where('transactions.is_retrieval', '1')
            ->orderBy('transactions.retrieval_date', 'desc');

        $data = $data->paginate($input['paginate']);

        return $data;
    }

    public function latest10Transactions()
    {
        $data = static::select('applications.business_name', 'transactions.*', 'middetails.bank_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->where('transactions.is_batch_transaction', '0')
            ->where('transactions.payment_gateway_id', '!=', '16')
            ->where('transactions.payment_gateway_id', '!=', '41')
            ->take(10)
            ->orderBy('id', 'DESC')->get();
        return $data;
    }

    public function getLatestRefundTransactionsAdminDash()
    {
        return static::where('refund', '1')
            ->latest()
            ->take(5)
            ->get();
    }

    public function getLatestChargebackTransactionsAdminDash()
    {
        return static::where('chargebacks', '1')
            ->latest()
            ->take(5)
            ->get();
    }

    public function getPayoutSummaryReporttt($input)
    {

        $query = '';

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $query = " and transactions.created_at >= '" . $start_date . " 00:00:00'" . " and " . "transactions.created_at <= '" . $end_date . " 23:59:59'";
        }

        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $query = $query . " and transactions.payment_gateway_id = '" . $input['payment_gateway_id'] . "'";
        }

        return static::select(
            'users.name as usersName',
            DB::raw("SUM(CASE WHEN (transactions.payment_gateway_id <> '41' and transactions.payment_gateway_id <> '16' and transactions.resubmit_transaction <> '2' and transactions.status = '1'" . $query . ") THEN transactions.amount ELSE 0 END) as success_amount"),
            DB::raw("SUM(CASE WHEN (transactions.payment_gateway_id <> '41' and transactions.payment_gateway_id <> '16' and transactions.resubmit_transaction <> '2' and transactions.status = '1'" . $query . ") THEN 1 ELSE 0 END) as success_count"),
            DB::raw("SUM(CASE WHEN (transactions.payment_gateway_id <> '41' and transactions.payment_gateway_id <> '16' and transactions.resubmit_transaction <> '2' and transactions.is_batch_transaction = '0' and transactions.chargebacks = '1' and transactions.refund = '1' and transactions.status = '0'" . $query . ") THEN transactions.amount ELSE 0 END) as declined_amount"),
            DB::raw("SUM(CASE WHEN (transactions.payment_gateway_id <> '41' and transactions.payment_gateway_id <> '16' and transactions.resubmit_transaction <> '2' and transactions.is_batch_transaction = '0' and transactions.chargebacks = '1' and transactions.refund = '1' and transactions.status = '0'" . $query . ") THEN 1 ELSE 0 END) as declined_count"),
            DB::raw("SUM(CASE WHEN (transactions.payment_gateway_id <> '41' and transactions.payment_gateway_id <> '16' and transactions.resubmit_transaction = '2' and transactions.is_batch_transaction = '0' and transactions.chargebacks = '1'" . $query . ") THEN transactions.amount ELSE 0 END) as chargebacks_amount"),
            DB::raw("SUM(CASE WHEN (transactions.payment_gateway_id <> '41' and transactions.payment_gateway_id <> '16' and transactions.resubmit_transaction = '2' and transactions.is_batch_transaction = '0' and transactions.chargebacks = '1'" . $query . ") THEN 1 ELSE 0 END) as chargebacks_count"),
            DB::raw("SUM(CASE WHEN (transactions.payment_gateway_id <> '41' and transactions.payment_gateway_id <> '16' and transactions.resubmit_transaction = '2' and transactions.is_batch_transaction = '0' and transactions.refund = '1'" . $query . ") THEN transactions.amount ELSE 0 END) as refund_amount"),
            DB::raw("SUM(CASE WHEN (transactions.payment_gateway_id <> '41' and transactions.payment_gateway_id <> '16' and transactions.resubmit_transaction = '2' and transactions.is_batch_transaction = '0' and transactions.refund = '1'" . $query . ") THEN 1 ELSE 0 END) as refund_count"),
            DB::raw("SUM(CASE WHEN (transactions.payment_gateway_id <> '41' and transactions.payment_gateway_id <> '16' and transactions.resubmit_transaction = '2' and transactions.is_batch_transaction = '0' and transactions.is_flagged = '1'" . $query . ") THEN transactions.amount ELSE 0 END) as flagged_amount"),
            DB::raw("SUM(CASE WHEN (transactions.payment_gateway_id <> '41' and transactions.payment_gateway_id <> '16' and transactions.resubmit_transaction = '2' and transactions.is_batch_transaction = '0' and transactions.is_flagged = '1'" . $query . ") THEN 1 ELSE 0 END) as flagged_count")
        )
            ->join('users', 'users.id', 'transactions.user_id')
            ->orderBy('transactions.created_at', 'DESC')
            ->groupBy('transactions.user_id')
            ->paginate(10);
    }

    public function getPayoutSummaryReport($input)
    {
        $data = static::select('transactions.*', 'users.name as usersName', 'applications.business_name as company_name')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id');
        if (
            (isset($input['start_date']) && $input['start_date'] != '') &&
            (isset($input['end_date']) && $input['end_date'] != '')
        ) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $start_date = $start_date . " 00:00:00";
            $end_date = $end_date . " 23:59:59";

            $data = $data->whereBetween('transactions.created_at', [$start_date, $end_date]);
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }
        $data = $data->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->orderBy('transactions.created_at', 'DESC')
            ->groupBy('transactions.user_id')
            ->paginate(30);

        return $data;
    }

    public function getPayoutSummaryReportLastSevenDays($input)
    {
        if (isset($input['start_date']) && isset($input['end_date']) && $input['start_date'] != '' && $input['end_date'] != '') {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $start_date = $start_date . " 00:00:00";
            $end_date = $end_date . " 23:59:59";
        } else {
            $start_date = Carbon::now()->subDays(7);
            $end_date = Carbon::now();
        }
        $data = static::select(
            'transactions.amount',
            'transactions.currency',
            'transactions.user_id',
            'users.name as usersName',
            \DB::raw("SUM(CASE WHEN (transactions.payment_gateway_id <> '41' and transactions.payment_gateway_id <> '16' and transactions.resubmit_transaction <> '2' and transactions.status = '1') THEN transactions.amount ELSE 0 END) as success_amount"),
            'applications.business_name as company_name'
        )
            ->join('users', function ($join) use ($input) {
                $join->on('users.id', '=', 'transactions.user_id')
                    ->where('users.main_user_id', '0')
                    ->where('users.is_active', '1');
            })
            ->join('applications', function ($join) use ($input) {
                $join->on('applications.user_id', '=', 'transactions.user_id');
            });
        $data = $data->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->whereBetween('transactions.created_at', [$start_date, $end_date])
            ->orderBy('success_amount', 'DESC')
            ->groupBy('transactions.user_id')
            ->get();

        dd($data);
    }

    public function getReportByMID($input)
    {
        $data = static::select('transactions.*', 'users.name as usersName', 'applications.business_name as company_name')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id');
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $data = $data->where('payment_gateway_id', $input['payment_gateway_id']);
        }
        $data = $data->orderBy('transactions.created_at', 'DESC')
            ->groupBy('transactions.user_id')
            ->paginate(10);

        return $data;
    }

    public function getReportByTransactionType($input)
    {
        $data = static::select('transactions.*', 'users.name as usersName', 'applications.business_name as company_name')
            ->join('users', 'users.id', 'transactions.user_id')
            ->join('applications', 'applications.user_id', 'transactions.user_id');
        if (isset($input['start_date']) && $input['start_date'] != '') {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        }
        if (isset($input['end_date']) && $input['end_date'] != '') {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('company_name', 'like', '%' . $input['company_name'] . '%');
        }
        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }
        $data = $data->where('payment_gateway_id', '<>', '16')
            ->where('payment_gateway_id', '<>', '41')
            ->where('resubmit_transaction', '<>', '2')
            ->orderBy('transactions.created_at', 'DESC')
            ->groupBy('transactions.user_id')
            ->paginate(10);
        return $data;
    }

    public function getCountryTotals($input)
    {

        $date_condition = "";
        $user_condition = "";

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $date_condition = "and created_at between '" . $start_date . "' and '" . $end_date . "' ";
        }
        // else {

        //     $date_condition = "and created_at between date_sub(now() , interval 31 day) and now() ";
        // }

        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $user_id = $input['user_id'];
            $user_condition = "and user_id = $user_id";
        }
        $table = '';

        $query = <<<SQL
    select  if (currency='','N/A',currency) currency, sum(volume) volume, sum(tx) as tx
    from

SQL;

        $where = <<<SQL
    where 1
    $user_condition
    $date_condition
    group by 1
SQL;


        $table = 'tx_success';
        $select = $query . $table . $where;
        $successD = \DB::select($select);
        $successD = (array) $successD;

        $table = 'tx_decline';
        $select = $query . $table . $where;
        $failD = \DB::select($select);
        $failD = (array) $failD;

        $table = 'tx_chargebacks';
        $select = $query . $table . $where;
        $chargebacksD = \DB::select($select);
        $chargebacksD = (array) $chargebacksD;

        $table = 'tx_refunds';
        $select = $query . $table . $where;
        $refundD = \DB::select($select);
        $refundD = (array) $refundD;

        $table = 'tx_flagged';
        $select = $query . $table . $where;
        $flaggdD = \DB::select($select);
        $flaggdD = (array) $flaggdD;

        $table = 'tx_pending';
        $select = $query . $table . $where;
        $pendingD = \DB::select($select);
        $pendingD = (array) $pendingD;

        return [
            'success' => $successD,
            'fail' => $failD,
            'chargebacks' => $chargebacksD,
            'refund' => $refundD,
            'flagged' => $flaggdD,
            'pending' => $pendingD
        ];
    }

    // ================================================
    /*  method : getActiveMerchants
     * @ param  :
     * @ Description :
     */// ==============================================
    public function getActiveMerchants()
    {
        $start_date = Carbon::now()->subDays(7);
        $end_date = Carbon::now();

        // return DB::table('transactions')
        //         ->select('user_id', DB::raw('sum(amount) as total'))
        //         ->groupBy('user_id')
        //         ->whereNull('transactions.deleted_at')
        //         ->where('status', '1')
        //         ->where('chargebacks', '<>', '1')
        //         ->where('refund', '<>', '1')
        //         ->where('is_flagged', '<>', '1')
        //         ->where('is_retrieval', '<>', '1')
        //         ->whereBetween('created_at', [$start_date, $end_date])
        //         ->whereNotIn('payment_gateway_id', ['16', '41'])
        //         ->get()
        //         ->where('total', '>=', '5000')
        //         ->count();


        $date_condition = "";
        $user_condition = "";


        // $start_date = date('Y-m-d', strtotime($input['start_date']));
        // $end_date = date('Y-m-d', strtotime($input['end_date']));

        $date_condition = "and created_at between '" . $start_date . "' and '" . $end_date . "' ";

        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $user_id = $input['user_id'];
            $user_condition = "and user_id = $user_id";
        }
        $table = '';

        $query = <<<SQL
    select  sum(volume) total_volume, sum(tx) as tx
    from

SQL;

        $where = <<<SQL
    where 1
    $user_condition
    $date_condition
    group by user_id
    having total_volume >= 5000
SQL;

        $table = 'tx_success';
        $select = $query . $table . $where;

        //dd($select);
        $successD = \DB::select($select);
        $successD = (array) $successD;

        //dd($successD);
        return sizeof($successD);
    }

    public function getActiveMerchantsArray()
    {
        $start_date = Carbon::now()->subDays(7);
        $end_date = Carbon::now();

        return DB::table('transactions')
            ->select('user_id', DB::raw('sum(amount) as total'))
            ->groupBy('user_id')
            ->whereNull('deleted_at')
            ->where('status', '1')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('is_retrieval', '<>', '1')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->whereNotIn('payment_gateway_id', ['16', '41'])
            ->get()
            ->where('total', '>=', '5000')
            ->pluck('user_id', 'user_id');
    }

    public function getPayoutSummaryReportByUser($userId, $input)
    {
        $start_date = $end_date = null;
        if (isset($input['start_date']) && $input['start_date'] != '') {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
        }
        if (isset($input['end_date']) && $input['end_date'] != '') {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
        }

        $currencyArray = ['USD', 'HKD', 'GBP', 'JPY', 'EUR', 'AUD', 'CAD', 'SGD', 'NZD', 'TWD', 'KRW', 'DKK', 'TRL', 'MYR', 'THB', 'INR', 'PHP', 'CHF', 'SEK', 'ILS', 'ZAR', 'RUB', 'NOK', 'AED', 'CNY'];

        $mainData = [];
        foreach ($currencyArray as $key => $value) {
            $chekTransactionInCurrency = static::where('payment_gateway_id', '<>', '16');
            $chekTransactionInCurrency = $chekTransactionInCurrency->where('currency', $value)
                ->count();

            if ($chekTransactionInCurrency > 0) {

                $total_approve_transaction_amount = static::where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_retrieval', '0')
                    ->where('currency', $value)
                    ->where('status', '1')
                    ->where('user_id', $userId);

                if (!empty($start_date)) {
                    $total_approve_transaction_amount = $total_approve_transaction_amount->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
                }

                if (!empty($end_date)) {
                    $total_approve_transaction_amount = $total_approve_transaction_amount->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
                }

                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $total_approve_transaction_amount = $total_approve_transaction_amount->where('payment_gateway_id', $input['payment_gateway_id']);
                }
                $total_approve_transaction_count = $total_approve_transaction_amount->get()->count();
                $total_approve_transaction_amount1 = $total_approve_transaction_amount->sum('amount');

                $total_declined_transaction_amount = static::where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('chargebacks', '<>', '1')
                    ->where('refund', '<>', '1')
                    ->where('currency', $value)
                    ->where('status', '0')
                    ->where('user_id', $userId);

                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $total_declined_transaction_amount = $total_declined_transaction_amount->whereBetween(DB::raw('date(created_at)'), [$start_date, $end_date]);
                }

                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $total_declined_transaction_amount = $total_declined_transaction_amount->where('payment_gateway_id', $input['payment_gateway_id']);
                }

                $total_declined_transaction_count = $total_declined_transaction_amount->get()->count();
                $total_declined_transaction_amount1 = $total_declined_transaction_amount->sum('amount');

                $total_chargebacks_transaction_amount = static::where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $value)
                    ->where('chargebacks', '1')
                    ->where('user_id', $userId);

                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $total_chargebacks_transaction_amount = $total_chargebacks_transaction_amount->whereBetween(DB::raw('date(chargebacks_date)'), [$start_date, $end_date]);
                }

                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $total_chargebacks_transaction_amount = $total_chargebacks_transaction_amount->where('payment_gateway_id', $input['payment_gateway_id']);
                }


                $total_chargebacks_transaction_count = $total_chargebacks_transaction_amount->get()->count();
                $total_chargebacks_transaction_amount1 = $total_chargebacks_transaction_amount->sum('amount');

                $total_refund_transaction_amount = static::where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $value)
                    ->where('refund', '1')
                    ->where('user_id', $userId);

                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $total_refund_transaction_amount = $total_refund_transaction_amount->whereBetween(DB::raw('date(refund_date)'), [$start_date, $end_date]);
                }

                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $total_refund_transaction_amount = $total_refund_transaction_amount->where('payment_gateway_id', $input['payment_gateway_id']);
                }

                $total_refund_transaction_count = $total_refund_transaction_amount->get()->count();
                $total_refund_transaction_amount1 = $total_refund_transaction_amount->sum('amount');

                $total_flagged_amount = static::where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $value)
                    ->where('is_flagged', '1')
                    ->where('user_id', $userId);

                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $total_flagged_amount = $total_flagged_amount->whereBetween(DB::raw('date(flagged_date)'), [$start_date, $end_date]);
                }

                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $total_flagged_amount = $total_flagged_amount->where('payment_gateway_id', $input['payment_gateway_id']);
                }

                $total_flagged_count = $total_flagged_amount->get()->count();
                $total_flagged_amount1 = $total_flagged_amount->sum('amount');

                $total_retrieval_amount = static::where('payment_gateway_id', '<>', '16')
                    ->where('payment_gateway_id', '<>', '41')
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $value)
                    ->where('is_retrieval', '1')
                    ->where('user_id', $userId);

                if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
                    $total_retrieval_amount = $total_retrieval_amount->whereBetween(DB::raw('date(retrieval_date)'), [$start_date, $end_date]);
                }

                if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                    $total_retrieval_amount = $total_retrieval_amount->where('payment_gateway_id', $input['payment_gateway_id']);
                }

                $total_retrieval_count = $total_retrieval_amount->get()->count();
                $total_retrieval_amount1 = $total_retrieval_amount->sum('amount');

                if ($total_approve_transaction_amount1 != 0 || $total_approve_transaction_count != 0 || $total_declined_transaction_amount1 != 0 || $total_declined_transaction_count != 0 || $total_chargebacks_transaction_amount1 != 0 || $total_chargebacks_transaction_count != 0 || $total_refund_transaction_amount1 != 0 || $total_refund_transaction_count != 0 || $total_flagged_amount1 != 0 || $total_flagged_count != 0 || $total_retrieval_count != 0 || $total_retrieval_amount1 != 0) {
                    $total_transaction_count = $total_approve_transaction_count + $total_declined_transaction_count + $total_chargebacks_transaction_count + $total_refund_transaction_count + $total_flagged_count + $total_retrieval_count;
                    $mainData[$value] = [
                        'total_approve_transaction_amount' => $total_approve_transaction_amount1,
                        'total_approve_transaction_count' => $total_approve_transaction_count,
                        'total_approve_transaction_percentage' => (($total_approve_transaction_count / $total_transaction_count) * 100) . '%',
                        'total_declined_transaction_amount' => $total_declined_transaction_amount1,
                        'total_declined_transaction_count' => $total_declined_transaction_count,
                        'total_declined_transaction_percentage' => (($total_declined_transaction_count / $total_transaction_count) * 100) . '%',
                        'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount1,
                        'total_chargebacks_transaction_count' => $total_chargebacks_transaction_count,
                        'total_chargebacks_transaction_percentage' => (($total_chargebacks_transaction_count / $total_transaction_count) * 100) . '%',
                        'total_refund_transaction_amount' => $total_refund_transaction_amount1,
                        'total_refund_transaction_count' => $total_refund_transaction_count,
                        'total_refund_transaction_percentage' => (($total_refund_transaction_count / $total_transaction_count) * 100) . '%',
                        'total_flagged_amount' => $total_flagged_amount1,
                        'total_flagged_count' => $total_flagged_count,
                        'total_flagged_transaction_percentage' => (($total_flagged_count / $total_transaction_count) * 100) . '%',
                        'total_retrieval_amount' => $total_retrieval_amount1,
                        'total_retrieval_count' => $total_retrieval_count,
                        'total_retrieval_transaction_percentage' => (($total_retrieval_count / $total_transaction_count) * 100) . '%',
                    ];
                }
            }
        }

        return $mainData;
    }
    public function getPayoutSummaryReportByMid($input)
    {
        $start_date = $end_date = null;
        $mainData = [];
        if (isset($input['start_date']) && $input['start_date'] != '') {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
        }
        if (isset($input['end_date']) && $input['end_date'] != '') {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
        }
        $getCurrency = static::where('payment_gateway_id', $input['payment_gateway_id']);
        if ($start_date !== null) {
            $getCurrency->whereDate('created_at', '>=', $start_date);
        }
        if ($end_date !== null) {
            $getCurrency->whereDate('created_at', '<=', $end_date);
        }

        $myCurrency = $getCurrency->pluck('currency')->toArray();
        $totalTrans = $getCurrency->count();

        $finalCurrency = array_unique($myCurrency);

        if (sizeof($finalCurrency) > 0) {

            foreach ($finalCurrency as $newCurrency) {
                //start approve transactions
                $total_approve_transaction_amount = static::where('payment_gateway_id', '=', $input['payment_gateway_id'])
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_retrieval', '0')
                    ->where('currency', $newCurrency)
                    ->where('status', '1');
                if (!empty($start_date)) {
                    $total_approve_transaction_amount = $total_approve_transaction_amount->whereDate('created_at', '>=', $start_date);
                }
                if (!empty($end_date)) {
                    $total_approve_transaction_amount = $total_approve_transaction_amount->whereDate('created_at', '<=', $end_date);
                }
                //$total_approve_transaction_count = $total_approve_transaction_amount->count();
                //$total_approve_transaction_amount1  = $total_approve_transaction_amount->sum('amount');
                $total_approve_transaction_amount_get = $total_approve_transaction_amount->pluck('amount')->toArray();
                $total_approve_transaction_amount1 = array_sum($total_approve_transaction_amount_get);
                $total_approve_transaction_count = sizeof($total_approve_transaction_amount_get);

                $total_approve_transaction_percentage = "0%";
                if ($total_approve_transaction_count !== "0") {
                    $math1 = (($total_approve_transaction_count / $totalTrans) * 100) . '%';
                    $total_approve_transaction_percentage = substr($math1, 0, 4);
                }
                //end approve transactions

                //strat declined transactions
                $total_declined_transaction_amount = static::where('payment_gateway_id', '=', $input['payment_gateway_id'])
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('chargebacks', '<>', '1')
                    ->where('refund', '<>', '1')
                    ->where('currency', $newCurrency)
                    ->where('status', '0');
                if ($start_date !== null) {
                    $total_declined_transaction_amount = $total_declined_transaction_amount->whereDate('created_at', '>=', $start_date);
                }
                if ($end_date !== null) {
                    $total_declined_transaction_amount = $total_declined_transaction_amount->whereDate('created_at', '<=', $end_date);
                }
                //$total_declined_transaction_count = $total_declined_transaction_amount->count();
                //$total_declined_transaction_amount1 = $total_declined_transaction_amount->sum('amount');
                $total_declined_transaction_amount_get = $total_declined_transaction_amount->pluck('amount')->toArray();
                $total_declined_transaction_count = sizeof($total_declined_transaction_amount_get);
                $total_declined_transaction_amount1 = array_sum($total_declined_transaction_amount_get);

                $total_declined_transaction_percentage = "0%";
                if ($total_declined_transaction_count !== "0") {
                    $math2 = (($total_declined_transaction_count / $totalTrans) * 100) . '%';
                    $total_declined_transaction_percentage = substr($math2, 0, 4);
                }
                //end declined transactions

                //strat chargebacks transactions
                $total_chargebacks_transaction_amount = static::where('payment_gateway_id', '=', $input['payment_gateway_id'])
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $newCurrency)
                    ->where('chargebacks', '1');
                if ($start_date !== null) {
                    $total_chargebacks_transaction_amount = $total_chargebacks_transaction_amount->whereDate('chargebacks_date', '>=', $start_date);
                }
                if ($end_date !== null) {
                    $total_chargebacks_transaction_amount = $total_chargebacks_transaction_amount->whereDate('chargebacks_date', '<=', $end_date);
                }
                //$total_chargebacks_transaction_count = $total_chargebacks_transaction_amount->count();
                //$total_chargebacks_transaction_amount1 = $total_chargebacks_transaction_amount->sum('amount');
                $total_chargebacks_transaction_amount_get = $total_chargebacks_transaction_amount->pluck('amount')->toArray();
                $total_chargebacks_transaction_count = sizeof($total_chargebacks_transaction_amount_get);
                $total_chargebacks_transaction_amount1 = array_sum($total_chargebacks_transaction_amount_get);

                $total_chargebacks_transaction_percentage = "0%";
                if ($total_chargebacks_transaction_count !== "0") {
                    $math3 = (($total_chargebacks_transaction_count / $totalTrans) * 100) . '%';
                    $total_chargebacks_transaction_percentage = substr($math3, 0, 4);
                }
                //end chargebacks transactions

                //start refund transactions
                $total_refund_transaction_amount = static::where('payment_gateway_id', '=', $input['payment_gateway_id'])
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $newCurrency)
                    ->where('refund', '1');
                if ($start_date !== null) {
                    $total_refund_transaction_amount = $total_refund_transaction_amount->whereDate('refund_date', '>=', $start_date);
                }
                if ($end_date !== null) {
                    $total_refund_transaction_amount = $total_refund_transaction_amount->whereDate('refund_date', '<=', $end_date);
                }
                //$total_refund_transaction_count = $total_refund_transaction_amount->count();
                //$total_refund_transaction_amount1 = $total_refund_transaction_amount->sum('amount');
                $total_refund_transaction_amount_get = $total_refund_transaction_amount->pluck('amount')->toArray();
                $total_refund_transaction_count = sizeof($total_refund_transaction_amount_get);
                $total_refund_transaction_amount1 = array_sum($total_refund_transaction_amount_get);

                $total_refund_transaction_percentage = "0%";
                if ($total_refund_transaction_count !== "0") {
                    $math4 = (($total_refund_transaction_count / $totalTrans) * 100) . '%';
                    $total_refund_transaction_percentage = substr($math4, 0, 4);
                }
                //end refund transactions
                //strat flagged transactions
                $total_flagged_amount = static::where('payment_gateway_id', '=', $input['payment_gateway_id'])
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $newCurrency)
                    ->where('is_flagged', '1');
                if ($start_date !== null) {
                    $total_flagged_amount = $total_flagged_amount->whereDate('flagged_date', '>=', $start_date);
                }
                if ($end_date !== null) {
                    $total_flagged_amount = $total_flagged_amount->whereDate('flagged_date', '<=', $end_date);
                }
                //$total_flagged_count = $total_flagged_amount->count();
                //$total_flagged_amount1 = $total_flagged_amount->sum('amount');
                $total_flagged_amount_get = $total_flagged_amount->pluck('amount')->toArray();
                $total_flagged_count = sizeof($total_flagged_amount_get);
                $total_flagged_amount1 = array_sum($total_flagged_amount_get);

                $total_flagged_transaction_percentage = "0%";
                if ($total_flagged_count !== "0") {
                    $math5 = (($total_flagged_count / $totalTrans) * 100) . '%';
                    $total_flagged_transaction_percentage = substr($math5, 0, 4);
                }
                //end flagged transactions

                //strat retrieval transacions
                $total_retrieval_amount = static::where('payment_gateway_id', '=', $input['payment_gateway_id'])
                    ->where('resubmit_transaction', '<>', '2')
                    ->where('is_batch_transaction', '0')
                    ->where('currency', $newCurrency)
                    ->where('is_retrieval', '1');
                if ($start_date !== null) {
                    $total_retrieval_amount = $total_retrieval_amount->whereDate('retrieval_date', '>=', $start_date);
                }
                if ($end_date !== null) {
                    $total_retrieval_amount = $total_retrieval_amount->whereDate('retrieval_date', '<=', $end_date);
                }
                //$total_retrieval_count = $total_retrieval_amount->count();
                //$total_retrieval_amount1 = $total_retrieval_amount->sum('amount');
                $total_retrieval_amount_get = $total_retrieval_amount->pluck('amount')->toArray();
                $total_retrieval_count = sizeof($total_retrieval_amount_get);
                $total_retrieval_amount1 = array_sum($total_retrieval_amount_get);


                $total_retrieval_transaction_percentage = "0%";
                if ($total_retrieval_count !== "0") {
                    $math6 = (($total_retrieval_count / $totalTrans) * 100) . '%';
                    $total_retrieval_transaction_percentage = substr($math6, 0, 4);
                }
                //end retrieval transacions
                if (isset($input['type']) && $input['type'] == "xlsx") {
                    $mainData[] = [
                        'total_approve_transaction_amount' => $total_approve_transaction_amount1,
                        'total_approve_transaction_count' => $total_approve_transaction_count,
                        'total_approve_transaction_percentage' => $total_approve_transaction_percentage,
                        'total_declined_transaction_amount' => $total_declined_transaction_amount1,
                        'total_declined_transaction_count' => $total_declined_transaction_count,
                        'total_declined_transaction_percentage' => $total_declined_transaction_percentage,
                        'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount1,
                        'total_chargebacks_transaction_count' => $total_chargebacks_transaction_count,
                        'total_chargebacks_transaction_percentage' => $total_chargebacks_transaction_percentage,
                        'total_refund_transaction_amount' => $total_refund_transaction_amount1,
                        'total_refund_transaction_count' => $total_refund_transaction_count,
                        'total_refund_transaction_percentage' => $total_refund_transaction_percentage,
                        'total_flagged_amount' => $total_flagged_amount1,
                        'total_flagged_count' => $total_flagged_count,
                        'total_flagged_transaction_percentage' => $total_flagged_transaction_percentage,
                        'total_retrieval_amount' => $total_retrieval_amount1,
                        'total_retrieval_count' => $total_retrieval_count,
                        'total_retrieval_transaction_percentage' => $total_retrieval_transaction_percentage
                    ];
                } else {
                    $mainData[$newCurrency] = [
                        'total_approve_transaction_amount' => $total_approve_transaction_amount1,
                        'total_approve_transaction_count' => $total_approve_transaction_count,
                        'total_approve_transaction_percentage' => $total_approve_transaction_percentage,
                        'total_declined_transaction_amount' => $total_declined_transaction_amount1,
                        'total_declined_transaction_count' => $total_declined_transaction_count,
                        'total_declined_transaction_percentage' => $total_declined_transaction_percentage,
                        'total_chargebacks_transaction_amount' => $total_chargebacks_transaction_amount1,
                        'total_chargebacks_transaction_count' => $total_chargebacks_transaction_count,
                        'total_chargebacks_transaction_percentage' => $total_chargebacks_transaction_percentage,
                        'total_refund_transaction_amount' => $total_refund_transaction_amount1,
                        'total_refund_transaction_count' => $total_refund_transaction_count,
                        'total_refund_transaction_percentage' => $total_refund_transaction_percentage,
                        'total_flagged_amount' => $total_flagged_amount1,
                        'total_flagged_count' => $total_flagged_count,
                        'total_flagged_transaction_percentage' => $total_flagged_transaction_percentage,
                        'total_retrieval_amount' => $total_retrieval_amount1,
                        'total_retrieval_count' => $total_retrieval_count,
                        'total_retrieval_transaction_percentage' => $total_retrieval_transaction_percentage
                    ];
                }
            }
            return $mainData;
        } else {
            return $mainData;
        }
        return $mainData;
    }
    public function mostFlaggedChargebacksReport($input)
    {
        $start_date = Carbon::parse($input['start_date'])->format('Y-m-d');
        $end_date = Carbon::parse($input['end_date'])->format('Y-m-d');
        $type = $input['type'];
        $finalMostData = [];
        if ($type == "is_flagged") {
            $data1 = static::whereDate('flagged_date', '>=', $start_date)
                ->whereDate('flagged_date', '<=', $end_date)
                ->where('is_flagged', '1');
            if (!empty($input['payment_gateway_id'])) {
                $data1 = $data1->where('payment_gateway_id', $input['payment_gateway_id']);
            }
            $data1 = $data1->pluck('user_id')->toArray();
            $mytest1 = array_count_values($data1);

            $finalMostData = ['data' => $mytest1, 'type' => $type];
        } else if ($type == "chargebacks") {
            $data2 = static::where('resubmit_transaction', '<>', '2')
                ->where('is_batch_transaction', '0')
                ->whereDate('chargebacks_date', '>=', $start_date)
                ->whereDate('chargebacks_date', '<=', $end_date)
                ->where('chargebacks', '1');
            if (!empty($input['payment_gateway_id'])) {
                $data2 = $data2->where('payment_gateway_id', $input['payment_gateway_id']);
            }
            $data2 = $data2->pluck('user_id')->toArray();
            $mytest2 = array_count_values($data2);

            $finalMostData = ['data' => $mytest2, 'type' => $type];
        } else if ($type == "is_retrieval") {
            $data3 = static::where('resubmit_transaction', '<>', '2')
                ->where('is_batch_transaction', '0')
                ->whereDate('retrieval_date', '>=', $start_date)
                ->whereDate('retrieval_date', '<=', $end_date)
                ->where('is_retrieval', '1');
            if (!empty($input['payment_gateway_id'])) {
                $data3 = $data3->where('payment_gateway_id', $input['payment_gateway_id']);
            }
            $data3 = $data3->pluck('user_id')->toArray();
            $mytest3 = array_count_values($data3);
            $finalMostData = ['data' => $mytest3, 'type' => $type];
        } else {
            $data4 = static::where('resubmit_transaction', '<>', '2')
                ->where('is_batch_transaction', '0')
                ->whereDate('refund_date', '>=', $start_date)
                ->whereDate('refund_date', '<=', $end_date)
                ->where('refund', '1');
            if (!empty($input['payment_gateway_id'])) {
                $data4 = $data4->where('payment_gateway_id', $input['payment_gateway_id']);
            }
            $data4 = $data4->pluck('user_id')->toArray();
            $mytest4 = array_count_values($data4);
            $finalMostData = ['data' => $mytest4, 'type' => $type];
        }
        return $finalMostData;
    }
    public function getAllBatchTransactionSearch($input)
    {
        $start_date = date('Y-m-d', strtotime($input['start_date']));
        $end_date = date('Y-m-d', strtotime($input['end_date']));

        $data = static::select('transactions.*')
            ->where('user_id', $input['company_name'])
            ->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date . ' 00:00:00')
            ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date . ' 23:59:59');

        if ((isset($input['select_from_mid']) && $input['select_from_mid'] != '')) {
            $data = $data->where('payment_gateway_id', $input['select_from_mid']);
        }

        if ((isset($input['card_type']) && $input['card_type'] != '')) {
            $data = $data->where('card_type', $input['card_type']);
        }

        $data = $data->get();

        return $data;
    }

    /*
    |=============================|
    | For A Agent Porpouse        |
    |=============================|
    */
    public function getAgentChartData($input)
    {
        // Success transaction count and amount
        $userIds = \DB::table('users')->where('agent_id', auth()->guard('agentUser')->user()->id)->pluck('id');
        $successTran = \DB::table('transactions')->select(DB::raw("SUM(amount) as successTranAmount"), DB::raw("count(*) as successcount"))->whereIn('user_id', $userIds);
        $successTran = $successTran->where('status', '1')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('transactions.is_retrieval', '0')
            ->whereNull('transactions.deleted_at')
            ->first();

        // Declined transaction count and amount
        $failTran = \DB::table('transactions')->select(DB::raw("SUM(amount) as failTranAmount"), DB::raw("count(*) as failCount"))->whereIn('user_id', $userIds);
        $failTran = $failTran->where('status', '0')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->whereNull('transactions.deleted_at')
            ->first();
        // Chargebacks transaction count and amount
        $chargebacksTran = \DB::table('transactions')->select(DB::raw("SUM(amount) as chargebacksTranAmount"), DB::raw("count(*) as chargebacksCount"))->whereIn('user_id', $userIds);
        $chargebacksTran = $chargebacksTran->where('chargebacks', '1')
            ->whereNull('transactions.deleted_at')
            ->first();
        // Refund transaction count and amount
        $refundTran = \DB::table('transactions')->select(DB::raw("SUM(amount) as refundTranAmount"), DB::raw("count(*) as refundCount"))->whereIn('user_id', $userIds);
        $refundTran = $refundTran->where('refund', '1')
            ->whereNull('transactions.deleted_at')
            ->first();
        // Flagged transaction count and amount
        $flaggedTran = \DB::table('transactions')->select(DB::raw("SUM(amount) as flaggedTranAmount"), DB::raw("count(*) as flaggedCount"))->whereIn('user_id', $userIds);
        $flaggedTran = $flaggedTran->where('is_flagged', '1')
            ->whereNull('transactions.deleted_at')
            ->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->first();
        // total transaction count and amount
        $totalTran = $successTran->successcount + $failTran->failCount + $chargebacksTran->chargebacksCount + $refundTran->refundCount + $flaggedTran->flaggedCount;
        $totalTranAmount = $successTran->successTranAmount + $failTran->failTranAmount + $chargebacksTran->chargebacksTranAmount + $refundTran->refundTranAmount + $flaggedTran->flaggedTranAmount;
        return [
            'success' => $successTran->successcount,
            'fail' => $failTran->failCount,
            'chargebacks' => $chargebacksTran->chargebacksCount,
            'refund' => $refundTran->refundCount,
            'flagged' => $flaggedTran->flaggedCount,
            'total' => $totalTran,
            'successamount' => $successTran->successTranAmount,
            'failamount' => $failTran->failTranAmount,
            'chargebacksamount' => $chargebacksTran->chargebacksTranAmount,
            'refundamount' => $refundTran->refundTranAmount,
            'flaggedamount' => $flaggedTran->flaggedTranAmount,
            'totalamount' => $totalTranAmount,
        ];
    }

    public function getAgentLineChartData($input)
    {
        $userIds = \DB::table('users')->where('agent_id', auth()->guard('agentUser')->user()->id)->pluck('id');
        $start_date = Carbon::now()->subDays(30);
        $end_date = Carbon::now();

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));
        }

        $successTran = \DB::table('transactions')
            ->select(\DB::raw('DATE_FORMAT(created_at,"%Y-%c-%e") as day'), \DB::raw('count(*) as user_count'))
            ->where('status', '1')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->where('transactions.is_retrieval', '0')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('created_at', [$start_date, $end_date]);
        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $successTran = $successTran->where('transactions.user_id', $input['user_id']);
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $successTran = $successTran->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        $successTran = $successTran->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->whereIn('user_id', $userIds)
            ->groupBy(\DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'))
            ->pluck('user_count', 'day');

        $refundTran = \DB::table('transactions')
            ->select(\DB::raw('DATE_FORMAT(created_at,"%Y-%c-%e") as day'), \DB::raw('count(*) as user_count'))
            ->where('refund', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('created_at', [$start_date, $end_date]);
        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $refundTran = $refundTran->where('transactions.user_id', $input['user_id']);
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $refundTran = $refundTran->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        $refundTran = $refundTran->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->whereIn('user_id', $userIds)
            ->groupBy(\DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'))
            ->pluck('user_count', 'day');

        $chargebacksTran = \DB::table('transactions')
            ->select(\DB::raw('DATE_FORMAT(created_at,"%Y-%c-%e") as day'), \DB::raw('count(*) as user_count'))
            ->where('chargebacks', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('created_at', [$start_date, $end_date]);
        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $chargebacksTran = $chargebacksTran->where('transactions.user_id', $input['user_id']);
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $chargebacksTran = $chargebacksTran->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        $chargebacksTran = $chargebacksTran->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->whereIn('user_id', $userIds)
            ->groupBy(\DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'))
            ->pluck('user_count', 'day');

        $failTran = \DB::table('transactions')
            ->select(\DB::raw('DATE_FORMAT(created_at,"%Y-%c-%e") as day'), \DB::raw('count(*) as user_count'))
            ->where('status', '0')
            ->where('chargebacks', '<>', '1')
            ->where('refund', '<>', '1')
            ->where('is_flagged', '<>', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('created_at', [$start_date, $end_date]);
        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $failTran = $failTran->where('transactions.user_id', $input['user_id']);
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $failTran = $failTran->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        $failTran = $failTran->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->whereIn('user_id', $userIds)
            ->groupBy(\DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'))
            ->pluck('user_count', 'day');

        $flaggedTran = \DB::table('transactions')
            ->select(\DB::raw('DATE_FORMAT(created_at,"%Y-%c-%e") as day'), \DB::raw('count(*) as user_count'))
            ->where('is_flagged', '1')
            ->where('resubmit_transaction', '<>', '2')
            ->where('is_batch_transaction', '0')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('created_at', [$start_date, $end_date]);
        if ((isset($input['user_id']) && $input['user_id'] != '')) {
            $flaggedTran = $flaggedTran->where('transactions.user_id', $input['user_id']);
        }
        if ((isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '')) {
            $flaggedTran = $flaggedTran->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        $flaggedTran = $flaggedTran->whereNotIn('transactions.payment_gateway_id', ['16', '41'])
            ->whereIn('user_id', $userIds)
            ->groupBy(\DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'))
            ->pluck('user_count', 'day');

        $data_array = [];
        $i = 0;
        while (strtotime($start_date) <= strtotime($end_date)) {
            $start_date = date("Y-n-j", strtotime($start_date));

            // date
            $data_array[$i][] = date("Y-m-d", strtotime($start_date));

            // success value
            if (isset($successTran[$start_date])) {
                $data_array[$i][] = $successTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // failed value
            if (isset($failTran[$start_date])) {
                $data_array[$i][] = $failTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // chargeback value
            if (isset($chargebacksTran[$start_date])) {
                $data_array[$i][] = $chargebacksTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // refund value
            if (isset($refundTran[$start_date])) {
                $data_array[$i][] = $refundTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            // flagged value
            if (isset($flaggedTran[$start_date])) {
                $data_array[$i][] = $flaggedTran[$start_date];
            } else {
                $data_array[$i][] = 0;
            }

            $i++;
            $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
        }
        return $data_array;
    }

    public function latest10TransactionsForAgent()
    {
        if (auth()->guard('agentUser')->user()->main_agent_id == 0) {
            $agentId = auth()->guard('agentUser')->user()->id;
        } else {
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $userIds = \DB::table('users')->where('agent_id', $agentId)->pluck('id');
        $data = static::select('applications.business_name', 'transactions.*', 'middetails.bank_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereIn('transactions.user_id', $userIds)
            ->take(10)
            ->whereNull('transactions.deleted_at')
            ->orderBy('id', 'DESC')->get();

        return $data;
    }

    public function latest10TransactionsForWLAgent()
    {
        $agentId = auth()->guard('agentUserWL')->user()->id;
        $userIds = \DB::table('users')->where('white_label_agent_id', $agentId)->pluck('id');

        $data = static::select('applications.business_name', 'transactions.*')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->whereIn('transactions.user_id', $userIds)
            ->take(10)
            ->whereNull('transactions.deleted_at')
            ->orderBy('id', 'DESC')->get();

        return $data;
    }

    public function getAllMerchantTransactionDataAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        if (auth()->guard('agentUser')->user()->main_agent_id == 0) {
            $agentId = auth()->guard('agentUser')->user()->id;
        } else {
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $userIds = \DB::table('users')->where('agent_id', $agentId)->pluck('id');

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->whereIn('transactions.user_id', $userIds)
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.amount', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.first_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.last_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere(DB::raw("CONCAT(transactions.first_name,' ',transactions.last_name)"), 'LIKE', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }

        $data = $data->paginate($noList);

        return $data;
    }

    public function getAllMerchantRefundTransactionDataAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        if (auth()->guard('agentUser')->user()->main_agent_id == 0) {
            $agentId = auth()->guard('agentUser')->user()->id;
        } else {
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $userIds = \DB::table('users')->where('agent_id', $agentId)->pluck('id');

        $data = static::select('applications.business_name', 'transactions.*', 'middetails.bank_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->orderBy('id', 'DESC');

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        } else {
            $data = $data->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.amount', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.first_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.last_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere(DB::raw("CONCAT(transactions.first_name,' ',transactions.last_name)"), 'LIKE', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = $input['end_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        //Refund date filter
        if ((isset($input['refund_start_date']) && $input['refund_start_date'] != '') && (isset($input['refund_end_date']) && $input['refund_end_date'] != '')) {
            $start_date = $input['refund_start_date'];
            $end_date = $input['refund_end_date'];

            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.refund_date)'), '<=', $end_date);
        } else if ((isset($input['refund_start_date']) && $input['refund_start_date'] != '') || (isset($input['refund_end_date']) && $input['refund_end_date'] == '')) {
            $start_date = $input['refund_start_date'];
            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '>=', $start_date);
        } else if ((isset($input['refund_start_date']) && $input['refund_start_date'] == '') || (isset($input['refund_end_date']) && $input['refund_end_date'] != '')) {
            $end_date = $input['refund_end_date'];
            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '<=', $end_date);
        }
        $data = $data->where('transactions.refund', '1')
            ->whereIn('transactions.user_id', $userIds);

        $data = $data->paginate($noList);

        return $data;
    }

    public function getAllMerchantFlaggedTransactionDataAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        if (auth()->guard('agentUser')->user()->main_agent_id == 0) {
            $agentId = auth()->guard('agentUser')->user()->id;
        } else {
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $userIds = \DB::table('users')->where('agent_id', $agentId)->pluck('id');

        $data = static::select('applications.business_name', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files', 'middetails.bank_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"flagged"'));
            })->orderBy('id', 'DESC');

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        } else {
            $data = $data->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.amount', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.first_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.last_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere(DB::raw("CONCAT(transactions.first_name,' ',transactions.last_name)"), 'LIKE', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = $input['end_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        //flagged date filter
        if ((isset($input['flagged_start_date']) && $input['flagged_start_date'] != '') && (isset($input['flagged_end_date']) && $input['flagged_end_date'] != '')) {
            $start_date = $input['flagged_start_date'];
            $end_date = $input['flagged_end_date'];

            $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.flagged_date)'), '<=', $end_date);
        } else if ((isset($input['flagged_start_date']) && $input['flagged_start_date'] != '') || (isset($input['flagged_end_date']) && $input['flagged_end_date'] == '')) {
            $start_date = $input['flagged_start_date'];
            $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '>=', $start_date);
        } else if ((isset($input['flagged_start_date']) && $input['flagged_start_date'] == '') || (isset($input['flagged_end_date']) && $input['flagged_end_date'] != '')) {
            $end_date = $input['flagged_end_date'];
            $data = $data->where(DB::raw('DATE(transactions.flagged_date)'), '<=', $end_date);
        }
        $data = $data->where('transactions.is_flagged', '1')
            ->whereIn('transactions.user_id', $userIds);

        $data = $data->paginate($noList);

        return $data;
    }

    public function getAllMerchantRetrievalTransactionDataAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        if (auth()->guard('agentUser')->user()->main_agent_id == 0) {
            $agentId = auth()->guard('agentUser')->user()->id;
        } else {
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $userIds = \DB::table('users')->where('agent_id', $agentId)->pluck('id');

        $data = static::select('applications.business_name', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files', 'middetails.bank_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"retrieval"'));
            })->orderBy('id', 'DESC');

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        } else {
            $data = $data->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }
        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.amount', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.first_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.last_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere(DB::raw("CONCAT(transactions.first_name,' ',transactions.last_name)"), 'LIKE', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = $input['end_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        //retrieval date filter
        if ((isset($input['retrieval_start_date']) && $input['retrieval_start_date'] != '') && (isset($input['retrieval_end_date']) && $input['retrieval_end_date'] != '')) {
            $start_date = $input['retrieval_start_date'];
            $end_date = $input['retrieval_end_date'];

            $data = $data->where(DB::raw('DATE(transactions.retrieval_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.retrieval_date)'), '<=', $end_date);
        } else if ((isset($input['retrieval_start_date']) && $input['retrieval_start_date'] != '') || (isset($input['retrieval_end_date']) && $input['retrieval_end_date'] == '')) {
            $start_date = $input['retrieval_start_date'];
            $data = $data->where(DB::raw('DATE(transactions.retrieval_date)'), '>=', $start_date);
        } else if ((isset($input['retrieval_start_date']) && $input['retrieval_start_date'] == '') || (isset($input['retrieval_end_date']) && $input['retrieval_end_date'] != '')) {
            $end_date = $input['retrieval_end_date'];
            $data = $data->where(DB::raw('DATE(transactions.retrieval_date)'), '<=', $end_date);
        }
        $data = $data->where('transactions.is_retrieval', '1')
            ->whereIn('transactions.user_id', $userIds);

        $data = $data->paginate($noList);

        return $data;
    }

    public function getAllMerchantChargebacksTransactionDataAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        if (auth()->guard('agentUser')->user()->main_agent_id == 0) {
            $agentId = auth()->guard('agentUser')->user()->id;
        } else {
            $agentId = auth()->guard('agentUser')->user()->main_agent_id;
        }

        $userIds = \DB::table('users')->where('agent_id', $agentId)->pluck('id');

        $data = static::select('applications.business_name', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files', 'middetails.bank_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"chargebacks"'));
            });

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        } else {
            $data = $data->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }
        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.amount', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.first_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.last_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere(DB::raw("CONCAT(transactions.first_name,' ',transactions.last_name)"), 'LIKE', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = $input['end_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        //chargebacks date filter
        if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] != '') && (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] != '')) {
            $start_date = $input['chargebacks_start_date'];
            $end_date = $input['chargebacks_end_date'];

            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', $end_date);
        } else if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] != '') || (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] == '')) {
            $start_date = $input['chargebacks_start_date'];
            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', $start_date);
        } else if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] == '') || (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] != '')) {
            $end_date = $input['chargebacks_end_date'];
            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', $end_date);
        }
        $data = $data->where('transactions.chargebacks', '1')
            ->whereIn('transactions.user_id', $userIds)
            ->orderBy('transactions.chargebacks_date', 'desc');
        $data = $data->paginate($noList);

        return $data;
    }

    public function getAllMerchantTransactionDataBank($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $this->Application = new Application;
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $data = $data->whereIn('transactions.user_id', $userWithMids['user_id']);
        } else {
            $data = $data->where('transactions.user_id', false);
        }
        if (isset($userWithMids['mid']) && !empty($userWithMids['mid'])) {
            $data = $data->whereIn('transactions.payment_gateway_id', $userWithMids['mid']);
        } else {
            $data = $data->where('transactions.payment_gateway_id', false);
        }
        $data = $data->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.amount', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.first_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.last_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere(DB::raw("CONCAT(transactions.first_name,' ',transactions.last_name)"), 'LIKE', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }

        $data = $data->paginate($noList);

        return $data;
    }

    public function getAllMerchantChargebacksTransactionDataBank($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $this->Application = new Application;
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);

        $data = static::select('applications.business_name', 'transactions.*', 'transactions_document_upload.files as transactions_document_upload_files', 'middetails.bank_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->leftjoin('transactions_document_upload', function ($join) {
                $join->on('transactions_document_upload.transaction_id', '=', 'transactions.id')
                    ->on('transactions_document_upload.files_for', '=', \DB::raw('"chargebacks"'));
            });

        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $data = $data->whereIn('transactions.user_id', $userWithMids['user_id']);
        } else {
            $data = $data->where('transactions.user_id', false);
        }
        if (isset($userWithMids['mid']) && !empty($userWithMids['mid'])) {
            $data = $data->whereIn('transactions.payment_gateway_id', $userWithMids['mid']);
        } else {
            $data = $data->where('transactions.payment_gateway_id', false);
        }
        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        } else {
            $data = $data->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }
        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.amount', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.first_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.last_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere(DB::raw("CONCAT(transactions.first_name,' ',transactions.last_name)"), 'LIKE', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        //chargebacks date filter
        if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] != '') && (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['chargebacks_start_date']));
            $end_date = date('Y-m-d', strtotime($input['chargebacks_end_date']));

            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', $end_date);
        } else if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] != '') || (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['chargebacks_start_date']));
            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '>=', $start_date);
        } else if ((isset($input['chargebacks_start_date']) && $input['chargebacks_start_date'] == '') || (isset($input['chargebacks_end_date']) && $input['chargebacks_end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['chargebacks_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.chargebacks_date)'), '<=', $end_date);
        }
        $data = $data->where('transactions.chargebacks', '1')
            ->orderBy('transactions.chargebacks_date', 'desc')
            ->paginate($noList);

        return $data;
    }

    public function getAllMerchantApprovedTransactionDataBank($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $this->Application = new Application;
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $data = $data->whereIn('transactions.user_id', $userWithMids['user_id']);
        } else {
            $data = $data->where('transactions.user_id', false);
        }
        if (isset($userWithMids['mid']) && !empty($userWithMids['mid'])) {
            $data = $data->whereIn('transactions.payment_gateway_id', $userWithMids['mid']);
        } else {
            $data = $data->where('transactions.payment_gateway_id', false);
        }
        $data = $data->where('transactions.status', 1)->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.amount', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.first_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.last_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere(DB::raw("CONCAT(transactions.first_name,' ',transactions.last_name)"), 'LIKE', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }

        $data = $data->paginate($noList);
        return $data;
    }

    public function getAllMerchantDeclinedTransactionDataBank($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $this->Application = new Application;
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $data = $data->whereIn('transactions.user_id', $userWithMids['user_id']);
        } else {
            $data = $data->where('transactions.user_id', false);
        }
        if (isset($userWithMids['mid']) && !empty($userWithMids['mid'])) {
            $data = $data->whereIn('transactions.payment_gateway_id', $userWithMids['mid']);
        } else {
            $data = $data->where('transactions.payment_gateway_id', false);
        }
        $data = $data->whereNotIn('transactions.status', [1, 2, 3, 4])
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.amount', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.first_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.last_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere(DB::raw("CONCAT(transactions.first_name,' ',transactions.last_name)"), 'LIKE', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }
        $data = $data->paginate($noList);
        return $data;
    }

    public function getAllMerchantRefundTransactionDataBank($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $this->Application = new Application;
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);

        $data = static::select('applications.business_name', 'transactions.*', 'middetails.bank_name')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id');

        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $data = $data->whereIn('transactions.user_id', $userWithMids['user_id']);
        } else {
            $data = $data->where('transactions.user_id', false);
        }
        if (isset($userWithMids['mid']) && !empty($userWithMids['mid'])) {
            $data = $data->whereIn('transactions.payment_gateway_id', $userWithMids['mid']);
        } else {
            $data = $data->where('transactions.payment_gateway_id', false);
        }
        $data = $data->where('transactions.refund', '1')->orderBy('id', 'DESC');

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        } else {
            $data = $data->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('applications.business_name', 'like', '%' . $input['company_name'] . '%');
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['global_search']) && $input['global_search'] != '') {
            $data = $data->where(function ($query) use ($input) {
                $query->orWhere('transactions.id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.order_id', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.descriptor', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('applications.business_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.phone_no', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.amount', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.first_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.last_name', 'like', '%' . $input['global_search'] . '%')
                    ->orWhere(DB::raw("CONCAT(transactions.first_name,' ',transactions.last_name)"), 'LIKE', '%' . $input['global_search'] . '%')
                    ->orWhere('transactions.email', 'like', '%' . $input['global_search'] . '%');
            });
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }
        //Refund date filter
        if ((isset($input['refund_start_date']) && $input['refund_start_date'] != '') && (isset($input['refund_end_date']) && $input['refund_end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['refund_start_date']));
            $end_date = date('Y-m-d', strtotime($input['refund_end_date']));

            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.refund_date)'), '<=', $end_date);
        } else if ((isset($input['refund_start_date']) && $input['refund_start_date'] != '') || (isset($input['refund_end_date']) && $input['refund_end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['refund_start_date']));
            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '>=', $start_date);
        } else if ((isset($input['refund_start_date']) && $input['refund_start_date'] == '') || (isset($input['refund_end_date']) && $input['refund_end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['refund_end_date']));
            $data = $data->where(DB::raw('DATE(transactions.refund_date)'), '<=', $end_date);
        }
        $data = $data->paginate($noList);
        return $data;
    }

    // ================================================
    /* method : getDataToMarkFlag
     * @param  :
     * @description : get data to mark flag
     */// ==============================================
    public function getDataToMarkFlag($input, $noList, $finalId)
    {
        $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
        $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));
        if (
            isset($input['include_email']) && $input['include_email'] == 'yes' &&
            isset($input['nos_email']) && $input['nos_email'] > 0
        ) {

            $email_array = static::select(
                'transactions.*',
                DB::raw('COUNT(transactions.email) as email_count'),
                DB::raw('COUNT(transactions.card_no) as card_count')
            )
                ->where('status', '1')
                ->where(DB::raw('DATE(created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(created_at)'), '<=', $end_date);


            if (isset($input['country']) && $input['country'] != '') {
                $email_array = $email_array->where('country', $input['country']);
            }
            if (isset($input['currency']) && $input['currency'] != '') {
                $email_array = $email_array->where('currency', $input['currency']);
            }
            if (isset($input['gateway_id']) && $input['gateway_id'] != '') {
                $email_array = $email_array->where('gateway_id', $input['gateway_id']);
            }
            if (isset($input['greater_then']) && $input['greater_then'] != '') {
                $email_array = $email_array->where('amount', '>=', $input['greater_then']);
            }
            if (isset($input['less_then']) && $input['less_then'] != '') {
                $email_array = $email_array->where('amount', '<=', $input['less_then']);
            }
            if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
                $email_array = $email_array->where('payment_gateway_id', $input['payment_gateway_id']);
            }
            if (isset($input['user_id']) && $input['user_id'] != '') {
                $email_array = $email_array->where('user_id', $input['user_id']);
            }

            $email_array = $email_array->groupBy('email')
                ->having('email_count', '>=', $input['nos_email'])
                ->pluck('email')
                ->toArray();
        } else {
            $email_array = [];
            $cardList = [];
        }
        $data = static::select(
            'applications.business_name',
            'transactions.*',
            'middetails.bank_name'
        )
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->leftJoin('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->where('transactions.status', '1')
            ->where('transactions.chargebacks', '0')
            ->where('transactions.is_retrieval', '0')
            ->where('transactions.refund', '0')
            ->where('transactions.is_flagged', '0')
            ->whereNull('transactions.flagged_date')
            ->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
            ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);

        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }
        if (isset($input['gateway_id']) && $input['gateway_id'] != '') {
            $data = $data->where('transactions.gateway_id', $input['gateway_id']);
        }
        if (isset($input['greater_then']) && $input['greater_then'] != '') {
            $data = $data->where('transactions.amount', '>=', $input['greater_then']);
        }
        if (isset($input['less_then']) && $input['less_then'] != '') {
            $data = $data->where('transactions.amount', '<=', $input['less_then']);
        }
        if (isset($input['payment_gateway_id']) && $input['payment_gateway_id'] != '') {
            $data = $data->where('transactions.payment_gateway_id', $input['payment_gateway_id']);
        }
        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }

        // card and email filter
        if (isset($input['include_card']) && $input['include_card'] == 'yes' && isset($input['nos_card']) && $input['nos_card'] > 0 && isset($input['include_email']) && $input['include_email'] == 'yes' && isset($input['nos_email']) && $input['nos_email'] > 0) {
            $data = $data->whereIn('transactions.id', $finalId)->whereIn('transactions.email', $email_array);
        } elseif (
            isset($input['include_email']) && $input['include_email'] == 'yes' && isset($input['nos_email']) && $input['nos_email'] > 0
        ) {
            $data = $data->whereIn('transactions.email', $email_array);
        } elseif (
            isset($input['include_card']) && $input['include_card'] == 'yes' && isset($input['nos_card']) && $input['nos_card'] > 0
        ) {
            $data = $data->whereIn('transactions.id', $finalId);
        }
        $dataId = $data->pluck('id')->toArray();
        $data = $data->orderBy('transactions.id', 'desc')->paginate($noList);
        $finalArray = ['ids' => $dataId, 'data' => $data];
        return $finalArray;
    }


    // Created by Hws Team
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    // Created by Hws Team

    public function getMerchantTransactionReport($input)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select(
            'transactions.user_id',
            'transactions.currency',
            'applications.business_name',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0)) refund_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),

            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage")

        )->leftJoin('applications', 'applications.user_id', '=', 'transactions.user_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }

        if (isset($input['currency']) && $input['currency'] != null) {
            $data = $data->where('transactions.currency', $input['currency']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));

            $data = $data->where('transactions.transaction_date', '>=', $start_date)
                ->where('transactions.transaction_date', '<=', $end_date);
        }

        if ((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) {

            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00'))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-6 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-30 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['success_per']) && $input['success_per'] != null) {
            $data = $data->having('success_percentage', '>', $input['success_per']);
        }

        if (isset($input['decline_per']) && $input['decline_per'] != null) {
            $data = $data->having('declined_percentage', '>', $input['decline_per']);
        }

        if (isset($input['chargebacks_per']) && $input['chargebacks_per'] != null) {
            $data = $data->having('chargebacks_percentage', '>', $input['chargebacks_per']);
        }

        if (isset($input['refund_per']) && $input['refund_per'] != null) {
            $data = $data->having('refund_percentage', '>', $input['refund_per']);
        }

        if (isset($input['suspicious_per']) && $input['suspicious_per'] != null) {
            $data = $data->having('flagged_percentage', '>', $input['suspicious_per']);
        }

        if (isset($input['retrieval_per']) && $input['retrieval_per'] != null) {
            $data = $data->having('retrieval_percentage', '>', $input['retrieval_per']);
        }

        if (isset($input['block_per']) && $input['block_per'] != null) {
            $data = $data->having('block_percentage', '>', $input['block_per']);
        }

        $data = $data->groupBy('transactions.user_id', 'transactions.currency')->orderBy('success_amount', 'desc')->get()->toArray();
        // ->toSql();
        // echo $data;exit();
        //->get()->toArray();

        return $data;
    }

    public function getTransactionSummaryRP($input, $isInternalMerchant = 0)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select(
            'transactions.user_id',
            'currency',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0)) refund_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),

            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount_in_usd, 0.00)) AS success_amount_in_usd")
        )->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $data = $data->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('user_id', $input['user_id']);
        }

        if (isset($input['currency']) && $input['currency'] != null) {
            $data = $data->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $data = $data->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $data = $data->groupBy('transactions.currency');
        }

        $data = $data->orderBy('success_amount', 'desc')->get()->toArray();

        // if ((isset($input['user_id']) && !empty($input['user_id']))) {
        //     $data = $this->getTodaysRecord($input);
        // }
        // if ((isset($input['currency']) && !empty($input['currency']))) {
        //     $data = $this->getTodaysRecord($input);
        // }
        // if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
        //     $data = $this->getTodaysRecord($input);
        // }
        // if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') && (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
        //     $data = $this->getTodaysRecord($input);
        // }
        // if (((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) && $isInternalMerchant == 0) {
        //     $data = $this->getTodaysRecord($input);
        // }

        // if (isset($input['for']) && $input['for'] == 'Weekly') {
        //     $data = $this->getTodaysRecord($input);
        // }

        // if (isset($input['for']) && $input['for'] == 'Monthly') {
        //     $data = $this->getTodaysRecord($input);
        // }

        return $data;
    }

    // * Get Merchant or Merchants transactions counts & percentages
    public function getMerchantTxnCountPercentage($payment_gateway_id, $user_id = null)
    {
        $transaction = DB::table("transactions as t")->selectRaw(
            config('transaction.success_amount') . " as successfullV,
                        " . config('transaction.success_count') . " as successfullC,
                        " . config('transaction.success_percentage') . " as successfullP,

                        " . config('transaction.declined_amount') . " as declinedV,
                        " . config('transaction.declined_count') . " as declinedC,
                        " . config('transaction.declined_percentage') . " as declinedP,

                        " . config('transaction.chargeback_amount') . " as chargebackV,
                        " . config('transaction.chargeback_count') . " as chargebackC,
                        " . config('transaction.chargeback_count') . " as chargebackP,

                        " . config('transaction.suspicious_amount') . " as suspiciousV,
                        " . config('transaction.suspicious_count') . " as suspiciousC,
                        " . config('transaction.suspicious_percentage') . " as suspiciousP,

                        " . config('transaction.refund_amount') . " as refundV,
                        " . config('transaction.refund_count') . " as refundC,
                        " . config('transaction.refund_percentage') . " as refundP",
        )->whereNotIn('t.payment_gateway_id', $payment_gateway_id)->where('t.deleted_at', NULL);

        if ($user_id) {
            $transaction->where('t.user_id', $user_id);
        }
        $transaction = $transaction->first();
        return $transaction;
    }

    public function getMerchantTransactionReports($input, $isInternalMerchant = 0)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select('created_at', 'id', 'order_id')->whereNotIn('payment_gateway_id', $payment_gateway_id);

        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $data = $data->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('user_id', $input['user_id']);
        } else {
            $data = $data->where('user_id', false);
        }

        $data = $data->groupBy(DB::raw('Date(created_at)'))->get()->toArray();
        if (!empty($data)) {
            for ($i = 0; $i < count($data); $i++) {

                $total_no_of_transactions_count = 0;
                $total_processing_amount = 0;
                $approved_amount = 0;
                $approved_count = 0;
                $declined_amount = 0;
                $declined_count = 0;
                $chargeback_amount = 0;
                $chargeback_count = 0;
                $refund_amount = 0;
                $refund_count = 0;
                $flagged_amount = 0;
                $flagged_count = 0;
                $retrieval_amount = 0;
                $retrieval_count = 0;
                $pre_arbitration_amount = 0;
                $pre_arbitration_count = 0;
                $mdr = 0;
                $reserve = 0;
                $transaction_fee = 0;
                $refund_fee = 0;
                $retrieval_fee = 0;
                $high_risk_transaction_fee = 0;
                $chargeback_fee = 0;
                $total_payable = 0;
                $gross_payable = 0;
                $net_payable = 0;
                $status = 0;

                $processing_amt = static::select('amount')->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
                    ->where('user_id', $input['user_id'])
                    ->whereDate(DB::raw('Date(transactions.created_at)'), date('Y-m-d', strtotime($data[$i]['created_at'])))
                    ->get()->toArray();

                $Aprroved_amt = static::select('amount')->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
                    ->where('user_id', $input['user_id'])
                    ->whereDate(DB::raw('Date(transactions.created_at)'), date('Y-m-d', strtotime($data[$i]['created_at'])))
                    ->where('status', '1')
                    ->where('chargebacks', '0')->where('chargebacks_remove', '0')
                    ->where('refund', '0')->where('refund_remove', '0')
                    ->where('is_flagged', '0')->where('is_flagged_remove', '0')
                    ->where('is_retrieval', '0')->where('is_retrieval_remove', '0')
                    ->get()->toArray();

                $declined_amt = static::select('amount')->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
                    ->where('user_id', $input['user_id'])
                    ->whereDate(DB::raw('Date(transactions.created_at)'), date('Y-m-d', strtotime($data[$i]['created_at'])))
                    ->where('status', '0')
                    ->where('chargebacks', '0')->where('chargebacks_remove', '0')
                    ->where('refund', '0')->where('refund_remove', '0')
                    ->where('is_flagged', '0')->where('is_flagged_remove', '0')
                    ->where('is_retrieval', '0')->where('is_retrieval_remove', '0')
                    ->get()->toArray();

                $chargeback_amt = static::select('amount')->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
                    ->where('user_id', $input['user_id'])
                    ->whereDate(DB::raw('Date(transactions.created_at)'), date('Y-m-d', strtotime($data[$i]['created_at'])))
                    ->where('status', '1')
                    ->where('chargebacks', '1')->where('chargebacks_remove', '0')
                    ->where('refund', '0')->where('refund_remove', '0')
                    ->where('is_flagged', '0')->where('is_flagged_remove', '0')
                    ->where('is_retrieval', '0')->where('is_retrieval_remove', '0')
                    ->get()->toArray();

                $refund_amt = static::select('amount')->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
                    ->where('user_id', $input['user_id'])
                    ->whereDate(DB::raw('Date(transactions.created_at)'), date('Y-m-d', strtotime($data[$i]['created_at'])))
                    ->where('status', '1')
                    ->where('refund', '1')->where('refund_remove', '0')
                    ->where('chargebacks', '0')->where('chargebacks_remove', '0')
                    ->where('is_flagged', '0')->where('is_flagged_remove', '0')
                    ->where('is_retrieval', '0')->where('is_retrieval_remove', '0')
                    ->get()->toArray();

                $flagged_amt = static::select('amount')->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
                    ->where('user_id', $input['user_id'])
                    ->whereDate(DB::raw('Date(transactions.created_at)'), date('Y-m-d', strtotime($data[$i]['created_at'])))
                    ->where('status', '1')
                    ->where('is_flagged', '1')->where('is_flagged_remove', '0')
                    ->where('chargebacks', '0')->where('chargebacks_remove', '0')
                    ->where('refund', '0')->where('refund_remove', '0')
                    ->where('is_retrieval', '0')->where('is_retrieval_remove', '0')
                    ->get()->toArray();

                $retrieval_amt = static::select('amount')->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
                    ->where('user_id', $input['user_id'])
                    ->whereDate(DB::raw('Date(transactions.created_at)'), date('Y-m-d', strtotime($data[$i]['created_at'])))
                    ->where('status', '1')
                    ->where('is_retrieval', '1')->where('is_retrieval_remove', '0')
                    ->where('is_flagged', '0')->where('is_flagged_remove', '0')
                    ->where('chargebacks', '0')->where('chargebacks_remove', '0')
                    ->where('refund', '0')->where('refund_remove', '0')
                    ->get()->toArray();

                $pre_arbitration_amt = static::select('amount')->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
                    ->where('user_id', $input['user_id'])
                    ->whereDate(DB::raw('Date(transactions.created_at)'), date('Y-m-d', strtotime($data[$i]['created_at'])))
                    ->where('is_pre_arbitration', '1')
                    ->get()->toArray();

                if (!empty($processing_amt)) {
                    foreach ($processing_amt as $processing_amt) {
                        $total_processing_amount += $processing_amt['amount'];
                    }
                }
                if (!empty($Aprroved_amt)) {
                    $approved_count = count($Aprroved_amt);
                    foreach ($Aprroved_amt as $approved) {
                        $approved_amount += $approved['amount'];
                    }
                }
                if (!empty($declined_amt)) {
                    $declined_count = count($declined_amt);
                    foreach ($declined_amt as $declined) {
                        $declined_amount += $declined['amount'];
                    }
                }
                $total_no_of_transactions_count = $approved_count + $declined_count;
                if (!empty($chargeback_amt)) {
                    $chargeback_count = count($chargeback_amt);
                    foreach ($chargeback_amt as $chargeback) {
                        $chargeback_amount += $chargeback['amount'];
                    }
                }
                if (!empty($refund_amt)) {
                    $refund_count = count($refund_amt);
                    foreach ($refund_amt as $refund) {
                        $refund_amount += $refund['amount'];
                    }
                }
                if (!empty($flagged_amt)) {
                    $flagged_count = count($flagged_amt);
                    foreach ($flagged_amt as $flagged) {
                        $flagged_amount += $flagged['amount'];
                    }
                }
                if (!empty($retrieval_amt)) {
                    $retrieval_count = count($retrieval_amt);
                    foreach ($retrieval_amt as $retrieval) {
                        $retrieval_amount += $retrieval['amount'];
                    }
                }
                if (!empty($pre_arbitration_amt)) {
                    $pre_arbitration_count = count($pre_arbitration_amt);
                    foreach ($pre_arbitration_amt as $pre_arbitration) {
                        $pre_arbitration_amount += $pre_arbitration['amount'];
                    }
                }

                $mdr = 0.07 * $approved_amount;
                $reserve = 0.1 * $approved_amount;
                $transaction_fee = 0.5 * $total_no_of_transactions_count;
                $refund_fee = 10 * $refund_count;
                $retrieval_fee = 50 * $retrieval_count;
                $high_risk_transaction_fee = 30 * $flagged_count;
                $chargeback_fee = 45 * $chargeback_count;
                $total_payable = $approved_amount - ($chargeback_amount + $refund_amount + $flagged_amount + $retrieval_amount + $pre_arbitration_amount + $mdr + $reserve + $transaction_fee + $refund_fee + $retrieval_fee + $high_risk_transaction_fee + $chargeback_fee);
                if ($i == 0) {
                    $gross_payable = $total_payable;
                    $old_total_payable = $total_payable;
                    $net_payable = $gross_payable;
                    $old_net_payable = $gross_payable;
                } else {
                    $gross_payable = $old_total_payable + $total_payable;
                    $old_total_payable = $gross_payable;
                    $net_payable = $old_net_payable + $total_payable;
                    $old_net_payable = $net_payable;
                }
                $data[$i]['created_date'] = date('d/m/Y', strtotime($data[$i]['created_at']));
                $data[$i]['total_processing_amount'] = $total_processing_amount;
                $data[$i]['approved_amount'] = $total_processing_amount;
                $data[$i]['declined_amount'] = $declined_amount;
                $data[$i]['chargeback_amount'] = $chargeback_amount;
                $data[$i]['refund_amount'] = $refund_amount;
                $data[$i]['flagged_amount'] = $flagged_amount;
                $data[$i]['retrieval_amount'] = $retrieval_amount;
                $data[$i]['pre_arbitration_amount'] = $pre_arbitration_amount;
                $data[$i]['approved_count'] = $approved_count;
                $data[$i]['declined_count'] = $declined_count;
                $data[$i]['chargeback_count'] = $chargeback_count;
                $data[$i]['refund_count'] = $refund_count;
                $data[$i]['flagged_count'] = $flagged_count;
                $data[$i]['retrieval_count'] = $retrieval_count;
                $data[$i]['pre_arbitration_count'] = $pre_arbitration_count;
                $data[$i]['total_no_of_transactions_count'] = $total_no_of_transactions_count;
                $data[$i]['mdr'] = $mdr;
                $data[$i]['reserve'] = $reserve;
                $data[$i]['transaction_fee'] = $transaction_fee;
                $data[$i]['refund_fee'] = $refund_fee;
                $data[$i]['retrieval_fee'] = $retrieval_fee;
                $data[$i]['high_risk_transaction_fee'] = $high_risk_transaction_fee;
                $data[$i]['chargeback_fee'] = $chargeback_fee;
                $data[$i]['total_payable'] = $total_payable;
                $data[$i]['gross_payable'] = $gross_payable;
                $data[$i]['net_payable'] = $net_payable;
                $data[$i]['status'] = $status;
            }
        }
        return $data;
    }

    public function getTransactionSummaryForRPMerchants($input, $isInternalMerchant = 0)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        if (!isset($input['user_id'])) {
            $user_id = \DB::table('applications')->join('users', 'users.id', 'applications.user_id')->where('agent_id', auth()->guard('agentUser')->user()->id)->pluck('user_id')->toArray();
            if (empty($user_id)) {
                $input['user_id'] = false;
                return [];
            } else {
                $input['user_id'] = $user_id;
            }
        } else {
            $user_id = \DB::table('applications')->join('users', 'users.id', 'applications.user_id')->where('agent_id', auth()->guard('agentUser')->user()->id)->pluck('user_id')->toArray();
            if (!empty($user_id)) {
                $input['user_id'] = $user_id;
            } else {
                if ($input['user_id'] == null) {
                    $input['user_id'] = false;
                    return [];
                }
            }
        }

        $data = static::select(
            'transactions.user_id',
            'currency',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0)) refund_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),

            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount_in_usd, 0.00)) AS success_amount_in_usd")
        )->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $data = $data->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('user_id', $input['user_id']);
        }

        if (isset($input['currency']) && $input['currency'] != null) {
            $data = $data->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $data = $data->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $data = $data->groupBy('transactions.currency');
        }

        $data = $data->orderBy('success_amount', 'desc')->get()->toArray();

        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $ArrRetunr = [];
            foreach ($data as $key => $value1) {
                $user_id = $value1['user_id'];
                $currency = $value1['currency'];
                $ArrRetunr[$user_id][$currency]['currency'] = $currency;
                if (isset($ArrRetunr[$user_id][$currency]['success_count'])) {
                    $ArrRetunr[$user_id][$currency]['success_count'] += $value1['success_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['success_count'] = $value1['success_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['success_amount'])) {
                    $ArrRetunr[$user_id][$currency]['success_amount'] += $value1['success_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['success_amount'] = $value1['success_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['success_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['success_percentage'] += $value1['success_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['success_percentage'] = $value1['success_percentage'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['declined_count'])) {
                    $ArrRetunr[$user_id][$currency]['declined_count'] += $value1['declined_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['declined_count'] = $value1['declined_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['declined_amount'])) {
                    $ArrRetunr[$user_id][$currency]['declined_amount'] += $value1['declined_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['declined_amount'] = $value1['declined_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['declined_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['declined_percentage'] += $value1['declined_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['declined_percentage'] = $value1['declined_percentage'];
                }

                if (isset($ArrRetunr[$user_id][$currency]['chargebacks_count'])) {
                    $ArrRetunr[$user_id][$currency]['chargebacks_count'] += $value1['chargebacks_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['chargebacks_count'] = $value1['chargebacks_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['chargebacks_amount'])) {
                    $ArrRetunr[$user_id][$currency]['chargebacks_amount'] += $value1['chargebacks_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['chargebacks_amount'] = $value1['chargebacks_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['chargebacks_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['refund_count'])) {
                    $ArrRetunr[$user_id][$currency]['refund_count'] += $value1['refund_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['refund_count'] = $value1['refund_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['refund_amount'])) {
                    $ArrRetunr[$user_id][$currency]['refund_amount'] += $value1['refund_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['refund_amount'] = $value1['refund_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['refund_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['refund_percentage'] += $value1['refund_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['refund_percentage'] = $value1['refund_percentage'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['flagged_count'])) {
                    $ArrRetunr[$user_id][$currency]['flagged_count'] += $value1['flagged_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['flagged_count'] = $value1['flagged_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['flagged_amount'])) {
                    $ArrRetunr[$user_id][$currency]['flagged_amount'] += $value1['flagged_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['flagged_amount'] = $value1['flagged_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['flagged_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['flagged_percentage'] += $value1['flagged_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['flagged_percentage'] = $value1['flagged_percentage'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['retrieval_count'])) {
                    $ArrRetunr[$user_id][$currency]['retrieval_count'] += $value1['retrieval_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['retrieval_count'] = $value1['retrieval_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['retrieval_amount'])) {
                    $ArrRetunr[$user_id][$currency]['retrieval_amount'] += $value1['retrieval_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['retrieval_amount'] = $value1['retrieval_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['retrieval_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['retrieval_percentage'] += $value1['retrieval_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['retrieval_percentage'] = $value1['retrieval_percentage'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['block_count'])) {
                    $ArrRetunr[$user_id][$currency]['block_count'] += $value1['block_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['block_count'] = $value1['block_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['block_amount'])) {
                    $ArrRetunr[$user_id][$currency]['block_amount'] += $value1['block_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['block_amount'] = $value1['block_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['block_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['block_percentage'] += $value1['block_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['block_percentage'] = $value1['block_percentage'];
                }
            }
            $data = $ArrRetunr;
        }

        if ((isset($input['user_id']) && !empty($input['user_id']))) {
            $data = $this->getTodaysRecordForRpMerchant($input);
        }
        if ((isset($input['currency']) && !empty($input['currency']))) {
            $data = $this->getTodaysRecordForRpMerchant($input);
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $data = $this->getTodaysRecordForRpMerchant($input);
        }
        // if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') && (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
        //     $data = $this->getTodaysRecord($input);
        // }
        if (((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) && $isInternalMerchant == 0) {
            $data = $this->getTodaysRecordForRpMerchant($input);
        }

        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $data = $this->getTodaysRecordForRpMerchant($input);
        }

        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $data = $this->getTodaysRecordForRpMerchant($input);
        }

        return $data;
    }

    public function getTransactionBankMerchantVolume($input, $isInternalMerchant = 0)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $this->Application = new Application;
        $userWithMids = $this->Application->getBankUserMids(auth()->guard('bankUser')->user()->id);

        $data = static::select(
            'transactions.user_id',
            'currency',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0)) refund_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),

            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount_in_usd, 0.00)) AS success_amount_in_usd")
        )->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);


        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $data = $data->whereIn('transactions.user_id', $userWithMids['user_id']);
        } else {
            $data = $data->where('transactions.user_id', false);
        }
        if (isset($userWithMids['mid']) && !empty($userWithMids['mid'])) {
            $data = $data->whereIn('transactions.payment_gateway_id', $userWithMids['mid']);
        } else {
            $data = $data->where('transactions.payment_gateway_id', false);
        }

        if (isset($input['currency']) && $input['currency'] != null) {
            $data = $data->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $data = $data->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $data = $data->groupBy('transactions.currency');
        }

        $data = $data->orderBy('success_amount', 'desc')->get()->toArray();

        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $ArrRetunr = [];
            foreach ($data as $key => $value1) {
                $user_id = $value1['user_id'];
                $currency = $value1['currency'];
                $ArrRetunr[$user_id][$currency]['currency'] = $currency;
                if (isset($ArrRetunr[$user_id][$currency]['success_count'])) {
                    $ArrRetunr[$user_id][$currency]['success_count'] += $value1['success_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['success_count'] = $value1['success_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['success_amount'])) {
                    $ArrRetunr[$user_id][$currency]['success_amount'] += $value1['success_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['success_amount'] = $value1['success_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['success_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['success_percentage'] += $value1['success_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['success_percentage'] = $value1['success_percentage'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['declined_count'])) {
                    $ArrRetunr[$user_id][$currency]['declined_count'] += $value1['declined_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['declined_count'] = $value1['declined_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['declined_amount'])) {
                    $ArrRetunr[$user_id][$currency]['declined_amount'] += $value1['declined_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['declined_amount'] = $value1['declined_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['declined_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['declined_percentage'] += $value1['declined_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['declined_percentage'] = $value1['declined_percentage'];
                }

                if (isset($ArrRetunr[$user_id][$currency]['chargebacks_count'])) {
                    $ArrRetunr[$user_id][$currency]['chargebacks_count'] += $value1['chargebacks_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['chargebacks_count'] = $value1['chargebacks_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['chargebacks_amount'])) {
                    $ArrRetunr[$user_id][$currency]['chargebacks_amount'] += $value1['chargebacks_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['chargebacks_amount'] = $value1['chargebacks_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['chargebacks_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['refund_count'])) {
                    $ArrRetunr[$user_id][$currency]['refund_count'] += $value1['refund_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['refund_count'] = $value1['refund_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['refund_amount'])) {
                    $ArrRetunr[$user_id][$currency]['refund_amount'] += $value1['refund_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['refund_amount'] = $value1['refund_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['refund_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['refund_percentage'] += $value1['refund_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['refund_percentage'] = $value1['refund_percentage'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['flagged_count'])) {
                    $ArrRetunr[$user_id][$currency]['flagged_count'] += $value1['flagged_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['flagged_count'] = $value1['flagged_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['flagged_amount'])) {
                    $ArrRetunr[$user_id][$currency]['flagged_amount'] += $value1['flagged_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['flagged_amount'] = $value1['flagged_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['flagged_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['flagged_percentage'] += $value1['flagged_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['flagged_percentage'] = $value1['flagged_percentage'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['retrieval_count'])) {
                    $ArrRetunr[$user_id][$currency]['retrieval_count'] += $value1['retrieval_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['retrieval_count'] = $value1['retrieval_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['retrieval_amount'])) {
                    $ArrRetunr[$user_id][$currency]['retrieval_amount'] += $value1['retrieval_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['retrieval_amount'] = $value1['retrieval_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['retrieval_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['retrieval_percentage'] += $value1['retrieval_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['retrieval_percentage'] = $value1['retrieval_percentage'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['block_count'])) {
                    $ArrRetunr[$user_id][$currency]['block_count'] += $value1['block_count'];
                } else {
                    $ArrRetunr[$user_id][$currency]['block_count'] = $value1['block_count'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['block_amount'])) {
                    $ArrRetunr[$user_id][$currency]['block_amount'] += $value1['block_amount'];
                } else {
                    $ArrRetunr[$user_id][$currency]['block_amount'] = $value1['block_amount'];
                }
                if (isset($ArrRetunr[$user_id][$currency]['block_percentage'])) {
                    $ArrRetunr[$user_id][$currency]['block_percentage'] += $value1['block_percentage'];
                } else {
                    $ArrRetunr[$user_id][$currency]['block_percentage'] = $value1['block_percentage'];
                }
            }
            $data = $ArrRetunr;
        }

        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $data = $this->getTodaysRecordForBankMerchantVolume($input, $userWithMids);
        }
        if ((isset($input['currency']) && !empty($input['currency']))) {
            $data = $this->getTodaysRecordForBankMerchantVolume($input, $userWithMids);
        }
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $data = $this->getTodaysRecordForBankMerchantVolume($input, $userWithMids);
        }
        // if ((isset($input['transaction_start_date']) && $input['transaction_start_date'] != '') && (isset($input['transaction_end_date']) && $input['transaction_end_date'] != '')) {
        //     $data = $this->getTodaysRecord($input);
        // }
        if (((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) && $isInternalMerchant == 0) {
            $data = $this->getTodaysRecordForBankMerchantVolume($input, $userWithMids);
        }

        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $data = $this->getTodaysRecordForBankMerchantVolume($input, $userWithMids);
        }

        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $data = $this->getTodaysRecordForBankMerchantVolume($input, $userWithMids);
        }

        return $data;
    }

    public function getTransactionSummaryRP2($input, $isInternalMerchant = 0)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        // if(isset($input['for']) && $input['for'] == "All"){

        //     $data = static::select( 'transactions.user_id', 'currency',
        //         DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
        //         DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
        //         DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
        //         DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
        //         DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
        //         DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

        //         DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count"),
        //         DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', amount, 0)) AS chargebacks_amount"),
        //         DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),

        //         DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0)) refund_count"),
        //         DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', amount, 0)) AS refund_amount"),
        //         DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),

        //         DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0)) AS flagged_count"),
        //         DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', amount, 0)) AS flagged_amount"),
        //         DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),

        //         DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
        //         DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
        //         DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),

        //         DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
        //         DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
        //         DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage"),
        //         DB::raw("SUM(IF(transactions.status = '1', transactions.amount_in_usd, 0.00)) AS success_amount_in_usd")
        //     )->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        //     if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
        //         $data = $data->whereIn('user_id', $input['user_id']);
        //     } else if (isset($input['user_id']) && $input['user_id'] != null) {
        //         $data = $data->where('user_id', $input['user_id']);
        //     }

        //     if (isset($input['currency']) && $input['currency'] != null) {
        //         $data = $data->where('currency', $input['currency']);
        //     }

        //     $data = $data->groupBy('transactions.currency')->orderBy('success_amount', 'desc')->get();
        //     dd($data);
        //     return $data;
        // }

        $start_date = $end_date = "";

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));
        }
        if ((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) {
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
        }
        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $start_date = date('Y-m-d 00:00:00', strtotime('-6 days'));
            $end_date = date('Y-m-d 23:59:59');
        }
        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $start_date = date('Y-m-d 23:59:59', strtotime('-30 days'));
            $end_date = date('Y-m-d 00:00:00');
        }
        $data = [];

        $data['success'] = DB::table('transactions')
            ->selectRaw("currency,
                    SUM(IF(transactions.status = '1', 1, 0)) as success_count,
                    SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount,
                    (SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage,
                    SUM(IF(transactions.status = '0', 1, 0)) as declined_count,
                    SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount,
                    (SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage,
                    SUM(IF(transactions.status = '5', 1, 0)) as block_count,
                    SUM(IF(transactions.status = '5' , transactions.amount,0.00 )) AS block_amount,
                    (SUM(IF(transactions.status = '5', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS block_percentage,
                    SUM(IF(transactions.status = '1', transactions.amount_in_usd, 0.00)) AS success_amount_in_usd
                ")
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        $success_count_query = "(select count(t.id) from transactions t where t.status = '1' AND t.currency = transactions.currency AND t.payment_gateway_id NOT IN(" . implode(',', $payment_gateway_id) . "))";

        if ($start_date && $end_date) {
            $data['success'] = $data['success']
                ->where('created_at', '>=', $start_date)
                ->where('created_at', '<=', $end_date);
        }

        if ($start_date && !isset($input['user_id'])) {
            $success_count_query = "(select count(t.id) from transactions t where t.status = '1' AND t.currency = transactions.currency AND t.created_at >= '" . $start_date . "' AND t.created_at <= '" . $end_date . "' AND t.payment_gateway_id NOT IN(" . implode(',', $payment_gateway_id) . "))";
        } elseif ($start_date && isset($input['user_id'])) {
            $success_count_query = "(select count(t.id) from transactions t where t.status = '1' AND t.currency = transactions.currency AND t.created_at >= '" . $start_date . "' AND t.created_at <= '" . $end_date . "' AND t.payment_gateway_id NOT IN(" . implode(',', $payment_gateway_id) . ") AND t.user_id=" . $input['user_id'] . ")";
        } elseif (isset($input['user_id']) && !$start_date) {
            $success_count_query = "(select count(t.id) from transactions t where t.status = '1' AND t.currency = transactions.currency AND t.payment_gateway_id NOT IN(" . implode(',', $payment_gateway_id) . ") AND t.user_id=" . $input['user_id'] . ")";
        }


        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data['success'] = $data['success']->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data['success'] = $data['success']->where('currency', $input['currency']);
        }
        $data['success'] = $data['success']->groupBy('currency')->orderBy('success_amount', 'desc')->get();

        $currency = $data['success']->pluck('currency')->toArray();

        $data['chargeback'] = DB::table('transactions')
            ->selectRaw("currency,
                    SUM(IF(transactions.chargebacks = '1', 1, 0)) AS chargebacks_count,
                    SUM(IF(transactions.chargebacks = '1', transactions.amount, 0)) AS chargebacks_amount
                    ")
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if ($start_date && $end_date) {
            $data['chargeback'] = $data['chargeback']->where('chargebacks_date', '>=', $start_date)
                ->where('chargebacks_date', '<=', $end_date);
        }

        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data['chargeback'] = $data['chargeback']->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data['chargeback'] = $data['chargeback']->where('currency', $input['currency']);
        }

        $data['chargeback'] = $data['chargeback']->groupBy('currency')->get();

        $currency_chargeback = $data['chargeback']->pluck('currency')->toArray();

        $data['refund'] = DB::table('transactions')
            ->selectRaw("currency,
                    SUM(IF(transactions.refund = '1', 1, 0)) AS refund_count,
                    SUM(IF(transactions.refund = '1', transactions.amount, 0)) AS refund_amount
                    ")
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if ($start_date && $end_date) {
            $data['refund'] = $data['refund']->where('refund_date', '>=', $start_date)
                ->where('refund_date', '<=', $end_date);
        }


        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data['refund'] = $data['refund']->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data['refund'] = $data['refund']->where('currency', $input['currency']);
        }

        $data['refund'] = $data['refund']
            ->groupBy('currency')
            ->get();
        $currency_refund = $data['refund']->pluck('currency')->toArray();

        $data['flagged'] = DB::table('transactions')
            ->selectRaw("currency,
                    SUM(IF(transactions.is_flagged = '1', 1, 0)) AS flagged_count,
                    SUM(IF(transactions.is_flagged = '1', transactions.amount, 0)) AS flagged_amount
                    ")
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if ($start_date && $end_date) {
            $data['flagged'] = $data['flagged']->where('flagged_date', '>=', $start_date)
                ->where('flagged_date', '<=', $end_date);
        }


        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data['flagged'] = $data['flagged']->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data['flagged'] = $data['flagged']->where('currency', $input['currency']);
        }

        $data['flagged'] = $data['flagged']
            ->groupBy('currency')
            ->get();

        $currency_flagged = $data['flagged']->pluck('currency')->toArray();


        $data['retrieval'] = DB::table('transactions')
            ->selectRaw("currency,
                    SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count,
                    SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount
                    ")
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if ($start_date && $end_date) {
            $data['retrieval'] = $data['retrieval']->where('retrieval_date', '>=', $start_date)
                ->where('retrieval_date', '<=', $end_date);
        }


        if (isset($input['user_id']) && $input['user_id'] != '') {
            $data['retrieval'] = $data['retrieval']->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != '') {
            $data['retrieval'] = $data['retrieval']->where('currency', $input['currency']);
        }

        $data['retrieval'] = $data['retrieval']
            ->groupBy('currency')
            ->get();
        // return $data;
        $currency_retrieval = $data['retrieval']->pluck('currency')->toArray();
        // dump($data);
        // dump($data_chargeback);
        // dump($data_refund);
        // dump($data_flagged);
        // dd($data_retrieval);
        $currency = array_unique(array_merge($currency, $currency_chargeback, $currency_refund, $currency_flagged, $currency_retrieval));
        $final = [];
        if (count($currency)) {
            foreach ($currency as $key => $value) {
                $final[$key]['currency'] = $value;
                $success = $data['success']->where('currency', $value)->first();
                $final[$key]['success_count'] = $success ? $success->success_count : 0;
                $final[$key]['success_amount'] = $success ? $success->success_amount : 0;
                $final[$key]['success_percentage'] = $success ? $success->success_percentage : 0;
                $final[$key]['success_percentage'] = $success ? $success->success_amount_in_usd : 0;
                $final[$key]['declined_count'] = $success ? $success->declined_count : 0;
                $final[$key]['declined_amount'] = $success ? $success->declined_amount : 0;
                $final[$key]['declined_percentage'] = $success ? $success->declined_percentage : 0;
                $final[$key]['block_count'] = $success ? $success->block_count : 0;
                $final[$key]['block_amount'] = $success ? $success->block_amount : 0;
                $final[$key]['block_percentage'] = $success ? $success->block_percentage : 0;
                $chargeback = $data['chargeback']->where('currency', $value)->first();
                $final[$key]['chargebacks_count'] = $chargeback ? $chargeback->chargebacks_count : 0;
                $final[$key]['chargebacks_amount'] = $chargeback ? $chargeback->chargebacks_amount : 0;
                $final[$key]['chargebacks_percentage'] = $chargeback && $final[$key]['success_count'] ? ($chargeback->chargebacks_count * 100) / $final[$key]['success_count'] : 0;
                $refund = $data['refund']->where('currency', $value)->first();
                $final[$key]['refund_count'] = $refund ? $refund->refund_count : 0;
                $final[$key]['refund_amount'] = $refund ? $refund->refund_amount : 0;
                $final[$key]['refund_percentage'] = $refund && $final[$key]['success_count'] ? ($refund->refund_count * 100) / $final[$key]['success_count'] : 0;
                $flagged = $data['flagged']->where('currency', $value)->first();
                $final[$key]['flagged_count'] = $flagged ? $flagged->flagged_count : 0;
                $final[$key]['flagged_amount'] = $flagged ? $flagged->flagged_amount : 0;
                $final[$key]['flagged_percentage'] = $flagged && $final[$key]['success_count'] ? ($flagged->flagged_count * 100) / $final[$key]['success_count'] : 0;
                $retrieval = $data['retrieval']->where('currency', $value)->first();
                $final[$key]['retrieval_count'] = $retrieval ? $retrieval->retrieval_count : 0;
                $final[$key]['retrieval_amount'] = $retrieval ? $retrieval->retrieval_amount : 0;
                $final[$key]['retrieval_percentage'] = $retrieval && $final[$key]['success_count'] ? ($retrieval->retrieval_count * 100) / $final[$key]['success_count'] : 0;
            }
        }

        return $final;

    }

    public function insertTransactionSummaryInNewTable($input)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];
        $data = static::select(
            'currency',
            'transactions.created_at',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0)) refund_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),

            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount_in_usd, 0.00)) AS success_amount_in_usd")
        )->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)->groupBy('transactions.currency', 'transactions.created_at')->get();





    }

    public function getCardSummaryReport($input)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select(
            'card_type',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0)) refund_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),

            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage")
        )->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('user_id', $input['user_id']);
        }

        if (isset($input['card_type']) && $input['card_type'] != null) {
            $data = $data->where('card_type', $input['card_type']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));

            $data = $data->where('transactions.transaction_date', '>=', $start_date)
                ->where('transactions.transaction_date', '<=', $end_date);
        }

        if ((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00'))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-6 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-30 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        $data = $data->groupBy('card_type')->orderBy('success_amount', 'desc')->get()->toArray();
        // ->toSql();
        // echo $data;exit();
        //->get()->toArray();

        return $data;
    }

    public function getPaymentStatusReport($input)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select(
            'transactions.user_id',
            'transactions.currency',
            'applications.business_name',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0)) refund_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),

            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage"),

        )->leftJoin('applications', 'applications.user_id', '=', 'transactions.user_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }

        if (isset($input['currency']) && $input['currency'] != null) {
            $data = $data->where('transactions.currency', $input['currency']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));

            $data = $data->where('transactions.transaction_date', '>=', $start_date)
                ->where('transactions.transaction_date', '<=', $end_date);
        }

        if ((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) {

            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00'))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-6 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-30 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        $data = $data->groupBy('transactions.user_id', 'transactions.currency')->orderBy('success_amount', 'desc')->get()->toArray();

        return $data;
    }

    public function getAllMerchantTransactionDataWLAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];


        $userIds = \DB::table('users')
            ->where('is_white_label', '1')
            ->where('white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('id');

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.customer_order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name', 'transactions.created_at', 'transactions.chargebacks', 'transactions.refund', 'transactions.created_at', 'transactions.reason', 'transactions.email', 'transactions.is_converted', 'transactions.converted_amount', 'transactions.converted_currency')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->whereIn('transactions.user_id', $userIds)
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }
        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }
        if (isset($input['customer_order_id']) && $input['customer_order_id'] != '') {
            $data = $data->where('transactions.customer_order_id', $input['customer_order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('transactions.user_id', $input['company_name']);
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }

        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }


        $data = $data->paginate($noList);
        return $data;
    }

    public function getMerchantCryptoTransactionDataWLAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $userIds = \DB::table('users')
            ->where('is_white_label', '1')
            ->where('white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('id');

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name', 'transactions.created_at')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->whereIn('transactions.user_id', $userIds)
            ->where('transactions.is_transaction_type', 'CRYPTO')
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('transactions.user_id', $input['company_name']);
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }

        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }

        $data = $data->paginate($noList);

        return $data;
    }

    public function getMerchantRefundTransactionDataWLAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $userIds = \DB::table('users')
            ->where('is_white_label', '1')
            ->where('white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('id');

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name', 'transactions.created_at', 'transactions.is_converted', 'transactions.converted_amount', 'transactions.converted_currency')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->whereIn('transactions.user_id', $userIds)
            ->where('transactions.chargebacks', '0')
            ->where('transactions.refund', '1')
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('transactions.user_id', $input['company_name']);
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }

        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }

        $data = $data->paginate($noList);

        return $data;
    }

    public function getMerchantChargebacksTransactionDataWLAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $userIds = \DB::table('users')
            ->where('is_white_label', '1')
            ->where('white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('id');

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name', 'transactions.created_at', 'transactions.is_converted', 'transactions.converted_amount', 'transactions.converted_currency')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->whereIn('transactions.user_id', $userIds)
            ->where('transactions.chargebacks', '1')
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('transactions.user_id', $input['company_name']);
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }

        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }

        $data = $data->paginate($noList);

        return $data;
    }

    public function getMerchantRetrievalTransactionDataWLAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $userIds = \DB::table('users')
            ->where('is_white_label', '1')
            ->where('white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('id');

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name', 'transactions.created_at', 'transactions.is_converted', 'transactions.converted_amount', 'transactions.converted_currency')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->whereIn('transactions.user_id', $userIds)
            ->where('transactions.chargebacks', '0')
            ->where('transactions.is_retrieval', '1')
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('transactions.user_id', $input['company_name']);
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }

        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }

        $data = $data->paginate($noList);

        return $data;
    }

    public function getMerchantSuspiciousTransactionDataWLAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $userIds = \DB::table('users')
            ->where('is_white_label', '1')
            ->where('white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('id');

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name', 'transactions.created_at', 'transactions.is_converted', 'transactions.converted_amount', 'transactions.converted_currency')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->whereIn('transactions.user_id', $userIds)
            ->where('transactions.chargebacks', '0')
            ->where('transactions.is_flagged', '1')
            ->where('transactions.is_flagged_remove', '0')
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('transactions.user_id', $input['company_name']);
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }

        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }

        $data = $data->paginate($noList);

        return $data;
    }

    public function getMerchantDeclinedTransactionDataWLAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $userIds = \DB::table('users')
            ->where('is_white_label', '1')
            ->where('white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('id');

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name', 'transactions.created_at')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->whereIn('transactions.user_id', $userIds)
            ->where('transactions.status', '0')
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('transactions.user_id', $input['company_name']);
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }

        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }

        $data = $data->paginate($noList);

        return $data;
    }

    public function getMerchantTestTransactionDataWLAgent($input, $noList)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $userIds = \DB::table('users')
            ->where('is_white_label', '1')
            ->where('white_label_agent_id', auth()->guard('agentUserWL')->user()->id)
            ->pluck('id');

        $data = static::select('applications.business_name', 'transactions.id', 'transactions.email', 'transactions.order_id', 'transactions.amount', 'transactions.currency', 'transactions.status', 'transactions.card_type', 'middetails.bank_name', 'transactions.first_name', 'transactions.last_name', 'transactions.created_at')
            ->join('applications', 'applications.user_id', 'transactions.user_id')
            ->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereIn('transactions.payment_gateway_id', $payment_gateway_id)
            ->whereIn('transactions.user_id', $userIds)
            ->orderBy('transactions.id', 'DESC');

        if (isset($input['first_name']) && $input['first_name'] != '') {
            $data = $data->where('transactions.first_name', 'like', '%' . $input['first_name'] . '%');
        }

        if (isset($input['last_name']) && $input['last_name'] != '') {
            $data = $data->where('transactions.last_name', 'like', '%' . $input['last_name'] . '%');
        }

        if (isset($input['status']) && $input['status'] != '') {
            $data = $data->where('transactions.status', $input['status']);
        }

        if (isset($input['order_id']) && $input['order_id'] != '') {
            $data = $data->where('transactions.order_id', $input['order_id']);
        }

        if (isset($input['company_name']) && $input['company_name'] != '') {
            $data = $data->where('transactions.user_id', $input['company_name']);
        }

        if (isset($input['email']) && $input['email'] != '') {
            $data = $data->where('transactions.email', 'like', '%' . $input['email'] . '%');
        }

        if (isset($input['country']) && $input['country'] != '') {
            $data = $data->where('transactions.country', $input['country']);
        }

        if (isset($input['currency']) && $input['currency'] != '') {
            $data = $data->where('transactions.currency', $input['currency']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = $input['start_date'];
            $end_date = $input['end_date'];

            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date)
                ->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = $input['start_date'];
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', $start_date);
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', $end_date);
        }

        $data = $data->paginate($noList);

        return $data;
    }

    public function getMidSummaryReport($input)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select(
            'middetails.bank_name',
            'transactions.currency',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0)) refund_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),

            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),

            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage")
        )->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($input['mid_type']) && $input['mid_type'] != null) {
            $data = $data->where('transactions.payment_gateway_id', $input['mid_type']);
        }

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));

            $data = $data->where('transactions.transaction_date', '>=', $start_date)
                ->where('transactions.transaction_date', '<=', $end_date);
        }

        if ((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00'))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-6 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-30 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        $data = $data->groupBy('transactions.payment_gateway_id', 'transactions.currency')->orderBy('success_amount', 'desc')->get()->toArray();
        // ->toSql();
        // echo $data;exit();
        //->get()->toArray();

        return $data;
    }

    public function getMidSummaryReportOnCountry($input)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $data = static::select(
            'transactions.country',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0.00)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),
            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage")
        )->join('middetails', 'middetails.id', 'transactions.payment_gateway_id')
            ->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);

        if (isset($input['mid_type']) && $input['mid_type'] != null) {
            $data = $data->where('transactions.payment_gateway_id', $input['mid_type']);
        }

        if (isset($input['user_id']) && $input['user_id'] != null) {
            $data = $data->where('transactions.user_id', $input['user_id']);
        }

        if (isset($input['country']) && $input['country'] != null) {
            $data = $data->where('transactions.country', $input['country']);
        }

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));

            $data = $data->where('transactions.transaction_date', '>=', $start_date)
                ->where('transactions.transaction_date', '<=', $end_date);
        }

        if ((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00'))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-6 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $data = $data->where('transactions.transaction_date', '>=', date('Y-m-d 00:00:00', strtotime('-30 days')))
                ->where('transactions.transaction_date', '<=', date('Y-m-d 23:59:59'));
        }

        $data = $data->groupBy('transactions.country');

        if (isset($input['operation']) && $input['operation'] != null) {
            if (isset($input['percentage']) && $input['percentage'] != null) {
                if ($input['operation'] == 'greaterthan') {
                    $data = $data->having('success_percentage', '>=', $input['percentage']);
                }
                if ($input['operation'] == 'lessthan') {
                    $data = $data->having('success_percentage', '<', $input['percentage']);
                }
            }
        }

        if (isset($input['sorting_operation']) && $input['sorting_operation'] != null) {
            if ($input['sorting_operation'] == 'ascending') {
                if ($input['sorting_status'] == 'successful') {
                    $data = $data->orderBy('success_percentage', 'asc')->get()->toArray();
                } elseif ($input['sorting_status'] == 'declined') {
                    $data = $data->orderBy('declined_percentage', 'asc')->get()->toArray();
                } elseif ($input['sorting_status'] == 'block') {
                    $data = $data->orderBy('block_percentage', 'asc')->get()->toArray();
                } else {
                    $data = $data->orderBy('success_percentage', 'asc')->get()->toArray();
                }
            }
            if ($input['sorting_operation'] == 'descending') {
                if ($input['sorting_status'] == 'successful') {
                    $data = $data->orderBy('success_percentage', 'desc')->get()->toArray();
                } elseif ($input['sorting_status'] == 'declined') {
                    $data = $data->orderBy('declined_percentage', 'desc')->get()->toArray();
                } elseif ($input['sorting_status'] == 'block') {
                    $data = $data->orderBy('block_percentage', 'desc')->get()->toArray();
                } else {
                    $data = $data->orderBy('success_percentage', 'desc')->get()->toArray();
                }
            }
        } else {
            $data = $data->orderBy('success_percentage', 'desc')->get()->toArray();
        }

        return $data;
    }

    public function getTodaysRecord($input)
    {
        $start_date = "";
        $end_date = "";
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));
        }
        if ((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) {
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
        }
        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $start_date = date('Y-m-d 00:00:00', strtotime('-6 days'));
            $end_date = date('Y-m-d 23:59:59');
        }
        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $start_date = date('Y-m-d 23:59:59', strtotime('-30 days'));
            $end_date = date('Y-m-d 00:00:00');
        }

        $today_chargeback_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("SUM(IF(transactions.chargebacks = '1', 1, 0)) AS chargebacks_count"),
            DB::raw("SUM(IF(transactions.chargebacks = '1', transactions.amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // ->where('transactions.status', '1')->where('transactions.chargebacks', '1');
        $today_chargeback_array = $today_chargeback_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_chargeback_array = $today_chargeback_array->where('transactions.chargebacks_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_chargeback_array = $today_chargeback_array->where('transactions.chargebacks_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_chargeback_array = $today_chargeback_array->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_chargeback_array = $today_chargeback_array->where('user_id', $input['user_id']);
        }

        if (isset($input['currency']) && $input['currency'] != null) {
            $today_chargeback_array = $today_chargeback_array->where('currency', $input['currency']);
        }

        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_chargeback_array = $today_chargeback_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_chargeback_array = $today_chargeback_array->groupBy('transactions.currency');
        }
        if (isset($input['chargebacks_per']) && !empty($input['chargebacks_per'])) {
            $today_chargeback_array = $today_chargeback_array->having('chargebacks_percentage', '>=', $input['chargebacks_per']);
        }
        $today_chargeback_array = $today_chargeback_array->orderBy('chargebacks_amount', 'DESC');

        $today_refund_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("SUM(IF(transactions.refund = '1', 1, 0)) AS refund_count"),
            DB::raw("SUM(IF(transactions.refund = '1', transactions.amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // $today_refund_array = $today_refund_array->where('transactions.status', '1')->where('transactions.refund', '1');
        $today_refund_array = $today_refund_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_refund_array = $today_refund_array->where('transactions.refund_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_refund_array = $today_refund_array->where('transactions.refund_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_refund_array = $today_refund_array->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_refund_array = $today_refund_array->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_refund_array = $today_refund_array->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_refund_array = $today_refund_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_refund_array = $today_refund_array->groupBy('transactions.currency');
        }
        if (isset($input['refund_per']) && $input['refund_per'] != null) {
            $today_refund_array = $today_refund_array->having('refund_percentage', '>', $input['refund_per']);
        }
        $today_refund_array = $today_refund_array->orderBy('refund_amount', 'DESC');

        $today_flagged_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("SUM(IF(transactions.is_flagged = '1', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.is_flagged = '1', transactions.amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // ->where('transactions.status', '1')->where('transactions.is_flagged', '1');
        $today_flagged_array = $today_flagged_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_flagged_array = $today_flagged_array->where('transactions.flagged_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_flagged_array = $today_flagged_array->where('transactions.flagged_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_flagged_array = $today_flagged_array->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_flagged_array = $today_flagged_array->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_flagged_array = $today_flagged_array->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_flagged_array = $today_flagged_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_flagged_array = $today_flagged_array->groupBy('transactions.currency');
        }
        if (isset($input['suspicious_per']) && $input['suspicious_per'] != null) {
            $today_flagged_array = $today_flagged_array->having('flagged_percentage', '>', $input['suspicious_per']);
        }
        $today_flagged_array = $today_flagged_array->orderBy('flagged_amount', 'DESC');

        $today_retrieval_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // ->where('transactions.status', '1')->where('transactions.is_flagged', '1');
        $today_retrieval_array = $today_retrieval_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_retrieval_array = $today_retrieval_array->where('transactions.retrieval_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_retrieval_array = $today_retrieval_array->where('transactions.retrieval_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_retrieval_array = $today_retrieval_array->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_retrieval_array = $today_retrieval_array->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_retrieval_array = $today_retrieval_array->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_retrieval_array = $today_retrieval_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_retrieval_array = $today_retrieval_array->groupBy('transactions.currency');
        }
        if (isset($input['retrieval_per']) && $input['retrieval_per'] != null) {
            $today_retrieval_array = $today_retrieval_array->having('retrieval_percentage', '>', $input['retrieval_per']);
        }
        $today_retrieval_array = $today_retrieval_array->orderBy('retrieval_amount', 'DESC');

        $today_success_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) AS success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0)) AS success_amount"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount_in_usd, 0.00)) AS success_amount_in_usd"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) AS declined_count"),
            DB::raw("SUM(IF(transactions.status = '0', transactions.amount, 0)) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage")
        );
        $today_success_array = $today_success_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_success_array = $today_success_array->where('transactions.created_at', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_success_array = $today_success_array->where('transactions.created_at', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_success_array = $today_success_array->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_success_array = $today_success_array->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_success_array = $today_success_array->where('currency', $input['currency']);
        }
        $today_success_array = $today_success_array->union($today_chargeback_array);
        $today_success_array = $today_success_array->union($today_refund_array);
        $today_success_array = $today_success_array->union($today_flagged_array);
        $today_success_array = $today_success_array->union($today_retrieval_array);
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_success_array = $today_success_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_success_array = $today_success_array->groupBy('transactions.currency');
        }
        if (isset($input['success_per']) && !empty($input['success_per'])) {
            $today_success_array = $today_success_array->having('success_percentage', '>=', $input['success_per']);
        }
        if (isset($input['decline_per']) && $input['decline_per'] != null) {
            $today_success_array = $today_success_array->having('declined_percentage', '>', $input['decline_per']);
        }
        if (isset($input['block_per']) && $input['block_per'] != null) {
            $today_success_array = $today_success_array->having('block_percentage', '>', $input['block_per']);
        }
        $today_success_array = $today_success_array->orderBy('success_amount', 'DESC');

        $data = $today_success_array->get();
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $data = $data->groupBy('user_id', 'currency');
        } else {
            $data = $data->groupBy('currency');
        }

        $ArrRetunr = [];
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            foreach ($data as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $user_id = $value1['user_id'];
                    $currency = $value1['currency'];
                    $ArrRetunr[$user_id][$currency]['currency'] = $currency;
                    if (isset($ArrRetunr[$user_id][$currency]['success_count'])) {
                        $ArrRetunr[$user_id][$currency]['success_count'] += $value1['success_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_count'] = $value1['success_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['success_amount'])) {
                        $ArrRetunr[$user_id][$currency]['success_amount'] += $value1['success_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_amount'] = $value1['success_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['success_amount_in_usd'])) {
                        $ArrRetunr[$user_id][$currency]['success_amount_in_usd'] += $value1['success_amount_in_usd'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_amount_in_usd'] = $value1['success_amount_in_usd'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['success_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['success_percentage'] += $value1['success_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_percentage'] = $value1['success_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['declined_count'])) {
                        $ArrRetunr[$user_id][$currency]['declined_count'] += $value1['declined_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['declined_count'] = $value1['declined_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['declined_amount'])) {
                        $ArrRetunr[$user_id][$currency]['declined_amount'] += $value1['declined_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['declined_amount'] = $value1['declined_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['declined_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['declined_percentage'] += $value1['declined_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['declined_percentage'] = $value1['declined_percentage'];
                    }

                    if (isset($ArrRetunr[$user_id][$currency]['chargebacks_count'])) {
                        $ArrRetunr[$user_id][$currency]['chargebacks_count'] += $value1['chargebacks_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['chargebacks_count'] = $value1['chargebacks_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['chargebacks_amount'])) {
                        $ArrRetunr[$user_id][$currency]['chargebacks_amount'] += $value1['chargebacks_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['chargebacks_amount'] = $value1['chargebacks_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['chargebacks_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['refund_count'])) {
                        $ArrRetunr[$user_id][$currency]['refund_count'] += $value1['refund_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['refund_count'] = $value1['refund_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['refund_amount'])) {
                        $ArrRetunr[$user_id][$currency]['refund_amount'] += $value1['refund_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['refund_amount'] = $value1['refund_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['refund_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['refund_percentage'] += $value1['refund_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['refund_percentage'] = $value1['refund_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['flagged_count'])) {
                        $ArrRetunr[$user_id][$currency]['flagged_count'] += $value1['flagged_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['flagged_count'] = $value1['flagged_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['flagged_amount'])) {
                        $ArrRetunr[$user_id][$currency]['flagged_amount'] += $value1['flagged_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['flagged_amount'] = $value1['flagged_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['flagged_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['flagged_percentage'] += $value1['flagged_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['flagged_percentage'] = $value1['flagged_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['retrieval_count'])) {
                        $ArrRetunr[$user_id][$currency]['retrieval_count'] += $value1['retrieval_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['retrieval_count'] = $value1['retrieval_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['retrieval_amount'])) {
                        $ArrRetunr[$user_id][$currency]['retrieval_amount'] += $value1['retrieval_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['retrieval_amount'] = $value1['retrieval_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['retrieval_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['retrieval_percentage'] += $value1['retrieval_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['retrieval_percentage'] = $value1['retrieval_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['block_count'])) {
                        $ArrRetunr[$user_id][$currency]['block_count'] += $value1['block_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['block_count'] = $value1['block_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['block_amount'])) {
                        $ArrRetunr[$user_id][$currency]['block_amount'] += $value1['block_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['block_amount'] = $value1['block_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['block_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['block_percentage'] += $value1['block_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['block_percentage'] = $value1['block_percentage'];
                    }
                }
            }
        } else {
            foreach ($data as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $currency = $value1['currency'];
                    $ArrRetunr[$currency]['currency'] = $currency;
                    if (isset($ArrRetunr[$currency]['success_count'])) {
                        $ArrRetunr[$currency]['success_count'] += $value1['success_count'];
                    } else {
                        $ArrRetunr[$currency]['success_count'] = $value1['success_count'];
                    }
                    if (isset($ArrRetunr[$currency]['success_amount'])) {
                        $ArrRetunr[$currency]['success_amount'] += $value1['success_amount'];
                    } else {
                        $ArrRetunr[$currency]['success_amount'] = $value1['success_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['success_amount_in_usd'])) {
                        $ArrRetunr[$currency]['success_amount_in_usd'] += $value1['success_amount_in_usd'];
                    } else {
                        $ArrRetunr[$currency]['success_amount_in_usd'] = $value1['success_amount_in_usd'];
                    }
                    if (isset($ArrRetunr[$currency]['success_percentage'])) {
                        $ArrRetunr[$currency]['success_percentage'] += $value1['success_percentage'];
                    } else {
                        $ArrRetunr[$currency]['success_percentage'] = $value1['success_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['declined_count'])) {
                        $ArrRetunr[$currency]['declined_count'] += $value1['declined_count'];
                    } else {
                        $ArrRetunr[$currency]['declined_count'] = $value1['declined_count'];
                    }
                    if (isset($ArrRetunr[$currency]['declined_amount'])) {
                        $ArrRetunr[$currency]['declined_amount'] += $value1['declined_amount'];
                    } else {
                        $ArrRetunr[$currency]['declined_amount'] = $value1['declined_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['declined_percentage'])) {
                        $ArrRetunr[$currency]['declined_percentage'] += $value1['declined_percentage'];
                    } else {
                        $ArrRetunr[$currency]['declined_percentage'] = $value1['declined_percentage'];
                    }

                    if (isset($ArrRetunr[$currency]['chargebacks_count'])) {
                        $ArrRetunr[$currency]['chargebacks_count'] += $value1['chargebacks_count'];
                    } else {
                        $ArrRetunr[$currency]['chargebacks_count'] = $value1['chargebacks_count'];
                    }
                    if (isset($ArrRetunr[$currency]['chargebacks_amount'])) {
                        $ArrRetunr[$currency]['chargebacks_amount'] += $value1['chargebacks_amount'];
                    } else {
                        $ArrRetunr[$currency]['chargebacks_amount'] = $value1['chargebacks_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['chargebacks_percentage'])) {
                        $ArrRetunr[$currency]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                    } else {
                        $ArrRetunr[$currency]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['refund_count'])) {
                        $ArrRetunr[$currency]['refund_count'] += $value1['refund_count'];
                    } else {
                        $ArrRetunr[$currency]['refund_count'] = $value1['refund_count'];
                    }
                    if (isset($ArrRetunr[$currency]['refund_amount'])) {
                        $ArrRetunr[$currency]['refund_amount'] += $value1['refund_amount'];
                    } else {
                        $ArrRetunr[$currency]['refund_amount'] = $value1['refund_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['refund_percentage'])) {
                        $ArrRetunr[$currency]['refund_percentage'] += $value1['refund_percentage'];
                    } else {
                        $ArrRetunr[$currency]['refund_percentage'] = $value1['refund_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['flagged_count'])) {
                        $ArrRetunr[$currency]['flagged_count'] += $value1['flagged_count'];
                    } else {
                        $ArrRetunr[$currency]['flagged_count'] = $value1['flagged_count'];
                    }
                    if (isset($ArrRetunr[$currency]['flagged_amount'])) {
                        $ArrRetunr[$currency]['flagged_amount'] += $value1['flagged_amount'];
                    } else {
                        $ArrRetunr[$currency]['flagged_amount'] = $value1['flagged_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['flagged_percentage'])) {
                        $ArrRetunr[$currency]['flagged_percentage'] += $value1['flagged_percentage'];
                    } else {
                        $ArrRetunr[$currency]['flagged_percentage'] = $value1['flagged_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['retrieval_count'])) {
                        $ArrRetunr[$currency]['retrieval_count'] += $value1['retrieval_count'];
                    } else {
                        $ArrRetunr[$currency]['retrieval_count'] = $value1['retrieval_count'];
                    }
                    if (isset($ArrRetunr[$currency]['retrieval_amount'])) {
                        $ArrRetunr[$currency]['retrieval_amount'] += $value1['retrieval_amount'];
                    } else {
                        $ArrRetunr[$currency]['retrieval_amount'] = $value1['retrieval_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['retrieval_percentage'])) {
                        $ArrRetunr[$currency]['retrieval_percentage'] += $value1['retrieval_percentage'];
                    } else {
                        $ArrRetunr[$currency]['retrieval_percentage'] = $value1['retrieval_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['block_count'])) {
                        $ArrRetunr[$currency]['block_count'] += $value1['block_count'];
                    } else {
                        $ArrRetunr[$currency]['block_count'] = $value1['block_count'];
                    }
                    if (isset($ArrRetunr[$currency]['block_amount'])) {
                        $ArrRetunr[$currency]['block_amount'] += $value1['block_amount'];
                    } else {
                        $ArrRetunr[$currency]['block_amount'] = $value1['block_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['block_percentage'])) {
                        $ArrRetunr[$currency]['block_percentage'] += $value1['block_percentage'];
                    } else {
                        $ArrRetunr[$currency]['block_percentage'] = $value1['block_percentage'];
                    }
                }
            }
        }
        return $ArrRetunr;
    }

    public function getTodaysRecordForRpMerchant($input)
    {
        $start_date = "";
        $end_date = "";
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));
        }
        if ((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) {
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
        }
        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $start_date = date('Y-m-d 00:00:00', strtotime('-6 days'));
            $end_date = date('Y-m-d 23:59:59');
        }
        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $start_date = date('Y-m-d 23:59:59', strtotime('-30 days'));
            $end_date = date('Y-m-d 00:00:00');
        }

        $today_chargeback_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("SUM(IF(transactions.chargebacks = '1', 1, 0)) AS chargebacks_count"),
            DB::raw("SUM(IF(transactions.chargebacks = '1', transactions.amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // ->where('transactions.status', '1')->where('transactions.chargebacks', '1');
        $today_chargeback_array = $today_chargeback_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_chargeback_array = $today_chargeback_array->where('transactions.chargebacks_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_chargeback_array = $today_chargeback_array->where('transactions.chargebacks_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_chargeback_array = $today_chargeback_array->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_chargeback_array = $today_chargeback_array->where('user_id', $input['user_id']);
        }

        if (isset($input['currency']) && $input['currency'] != null) {
            $today_chargeback_array = $today_chargeback_array->where('currency', $input['currency']);
        }

        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_chargeback_array = $today_chargeback_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_chargeback_array = $today_chargeback_array->groupBy('transactions.currency');
        }
        if (isset($input['chargebacks_per']) && !empty($input['chargebacks_per'])) {
            $today_chargeback_array = $today_chargeback_array->having('chargebacks_percentage', '>=', $input['chargebacks_per']);
        }
        $today_chargeback_array = $today_chargeback_array->orderBy('chargebacks_amount', 'DESC');

        $today_refund_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("SUM(IF(transactions.refund = '1', 1, 0)) AS refund_count"),
            DB::raw("SUM(IF(transactions.refund = '1', transactions.amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // $today_refund_array = $today_refund_array->where('transactions.status', '1')->where('transactions.refund', '1');
        $today_refund_array = $today_refund_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_refund_array = $today_refund_array->where('transactions.refund_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_refund_array = $today_refund_array->where('transactions.refund_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_refund_array = $today_refund_array->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_refund_array = $today_refund_array->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_refund_array = $today_refund_array->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_refund_array = $today_refund_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_refund_array = $today_refund_array->groupBy('transactions.currency');
        }
        if (isset($input['refund_per']) && $input['refund_per'] != null) {
            $today_refund_array = $today_refund_array->having('refund_percentage', '>', $input['refund_per']);
        }
        $today_refund_array = $today_refund_array->orderBy('refund_amount', 'DESC');

        $today_flagged_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("SUM(IF(transactions.is_flagged = '1', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.is_flagged = '1', transactions.amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // ->where('transactions.status', '1')->where('transactions.is_flagged', '1');
        $today_flagged_array = $today_flagged_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_flagged_array = $today_flagged_array->where('transactions.flagged_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_flagged_array = $today_flagged_array->where('transactions.flagged_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_flagged_array = $today_flagged_array->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_flagged_array = $today_flagged_array->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_flagged_array = $today_flagged_array->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_flagged_array = $today_flagged_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_flagged_array = $today_flagged_array->groupBy('transactions.currency');
        }
        if (isset($input['suspicious_per']) && $input['suspicious_per'] != null) {
            $today_flagged_array = $today_flagged_array->having('flagged_percentage', '>', $input['suspicious_per']);
        }
        $today_flagged_array = $today_flagged_array->orderBy('flagged_amount', 'DESC');

        $today_retrieval_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // ->where('transactions.status', '1')->where('transactions.is_flagged', '1');
        $today_retrieval_array = $today_retrieval_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_retrieval_array = $today_retrieval_array->where('transactions.retrieval_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_retrieval_array = $today_retrieval_array->where('transactions.retrieval_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_retrieval_array = $today_retrieval_array->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_retrieval_array = $today_retrieval_array->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_retrieval_array = $today_retrieval_array->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_retrieval_array = $today_retrieval_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_retrieval_array = $today_retrieval_array->groupBy('transactions.currency');
        }
        if (isset($input['retrieval_per']) && $input['retrieval_per'] != null) {
            $today_retrieval_array = $today_retrieval_array->having('retrieval_percentage', '>', $input['retrieval_per']);
        }
        $today_retrieval_array = $today_retrieval_array->orderBy('retrieval_amount', 'DESC');

        $today_success_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) AS success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0)) AS success_amount"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount_in_usd, 0.00)) AS success_amount_in_usd"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) AS declined_count"),
            DB::raw("SUM(IF(transactions.status = '0', transactions.amount, 0)) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage")
        );
        $today_success_array = $today_success_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_success_array = $today_success_array->where('transactions.created_at', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_success_array = $today_success_array->where('transactions.created_at', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_success_array = $today_success_array->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_success_array = $today_success_array->where('user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_success_array = $today_success_array->where('currency', $input['currency']);
        }
        $today_success_array = $today_success_array->union($today_chargeback_array);
        $today_success_array = $today_success_array->union($today_refund_array);
        $today_success_array = $today_success_array->union($today_flagged_array);
        $today_success_array = $today_success_array->union($today_retrieval_array);
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_success_array = $today_success_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_success_array = $today_success_array->groupBy('transactions.currency');
        }
        if (isset($input['success_per']) && !empty($input['success_per'])) {
            $today_success_array = $today_success_array->having('success_percentage', '>=', $input['success_per']);
        }
        if (isset($input['decline_per']) && $input['decline_per'] != null) {
            $today_success_array = $today_success_array->having('declined_percentage', '>', $input['decline_per']);
        }
        if (isset($input['block_per']) && $input['block_per'] != null) {
            $today_success_array = $today_success_array->having('block_percentage', '>', $input['block_per']);
        }
        $today_success_array = $today_success_array->orderBy('success_amount', 'DESC');

        $data = $today_success_array->get();
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $data = $data->groupBy('user_id', 'currency');
        } else {
            $data = $data->groupBy('currency');
        }

        $ArrRetunr = [];
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            foreach ($data as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $user_id = $value1['user_id'];
                    $currency = $value1['currency'];
                    $ArrRetunr[$user_id][$currency]['currency'] = $currency;
                    if (isset($ArrRetunr[$user_id][$currency]['success_count'])) {
                        $ArrRetunr[$user_id][$currency]['success_count'] += $value1['success_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_count'] = $value1['success_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['success_amount'])) {
                        $ArrRetunr[$user_id][$currency]['success_amount'] += $value1['success_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_amount'] = $value1['success_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['success_amount_in_usd'])) {
                        $ArrRetunr[$user_id][$currency]['success_amount_in_usd'] += $value1['success_amount_in_usd'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_amount_in_usd'] = $value1['success_amount_in_usd'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['success_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['success_percentage'] += $value1['success_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_percentage'] = $value1['success_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['declined_count'])) {
                        $ArrRetunr[$user_id][$currency]['declined_count'] += $value1['declined_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['declined_count'] = $value1['declined_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['declined_amount'])) {
                        $ArrRetunr[$user_id][$currency]['declined_amount'] += $value1['declined_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['declined_amount'] = $value1['declined_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['declined_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['declined_percentage'] += $value1['declined_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['declined_percentage'] = $value1['declined_percentage'];
                    }

                    if (isset($ArrRetunr[$user_id][$currency]['chargebacks_count'])) {
                        $ArrRetunr[$user_id][$currency]['chargebacks_count'] += $value1['chargebacks_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['chargebacks_count'] = $value1['chargebacks_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['chargebacks_amount'])) {
                        $ArrRetunr[$user_id][$currency]['chargebacks_amount'] += $value1['chargebacks_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['chargebacks_amount'] = $value1['chargebacks_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['chargebacks_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['refund_count'])) {
                        $ArrRetunr[$user_id][$currency]['refund_count'] += $value1['refund_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['refund_count'] = $value1['refund_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['refund_amount'])) {
                        $ArrRetunr[$user_id][$currency]['refund_amount'] += $value1['refund_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['refund_amount'] = $value1['refund_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['refund_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['refund_percentage'] += $value1['refund_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['refund_percentage'] = $value1['refund_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['flagged_count'])) {
                        $ArrRetunr[$user_id][$currency]['flagged_count'] += $value1['flagged_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['flagged_count'] = $value1['flagged_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['flagged_amount'])) {
                        $ArrRetunr[$user_id][$currency]['flagged_amount'] += $value1['flagged_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['flagged_amount'] = $value1['flagged_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['flagged_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['flagged_percentage'] += $value1['flagged_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['flagged_percentage'] = $value1['flagged_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['retrieval_count'])) {
                        $ArrRetunr[$user_id][$currency]['retrieval_count'] += $value1['retrieval_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['retrieval_count'] = $value1['retrieval_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['retrieval_amount'])) {
                        $ArrRetunr[$user_id][$currency]['retrieval_amount'] += $value1['retrieval_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['retrieval_amount'] = $value1['retrieval_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['retrieval_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['retrieval_percentage'] += $value1['retrieval_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['retrieval_percentage'] = $value1['retrieval_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['block_count'])) {
                        $ArrRetunr[$user_id][$currency]['block_count'] += $value1['block_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['block_count'] = $value1['block_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['block_amount'])) {
                        $ArrRetunr[$user_id][$currency]['block_amount'] += $value1['block_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['block_amount'] = $value1['block_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['block_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['block_percentage'] += $value1['block_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['block_percentage'] = $value1['block_percentage'];
                    }
                }
            }
        } else {
            foreach ($data as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $currency = $value1['currency'];
                    $ArrRetunr[$currency]['currency'] = $currency;
                    if (isset($ArrRetunr[$currency]['success_count'])) {
                        $ArrRetunr[$currency]['success_count'] += $value1['success_count'];
                    } else {
                        $ArrRetunr[$currency]['success_count'] = $value1['success_count'];
                    }
                    if (isset($ArrRetunr[$currency]['success_amount'])) {
                        $ArrRetunr[$currency]['success_amount'] += $value1['success_amount'];
                    } else {
                        $ArrRetunr[$currency]['success_amount'] = $value1['success_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['success_amount_in_usd'])) {
                        $ArrRetunr[$currency]['success_amount_in_usd'] += $value1['success_amount_in_usd'];
                    } else {
                        $ArrRetunr[$currency]['success_amount_in_usd'] = $value1['success_amount_in_usd'];
                    }
                    if (isset($ArrRetunr[$currency]['success_percentage'])) {
                        $ArrRetunr[$currency]['success_percentage'] += $value1['success_percentage'];
                    } else {
                        $ArrRetunr[$currency]['success_percentage'] = $value1['success_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['declined_count'])) {
                        $ArrRetunr[$currency]['declined_count'] += $value1['declined_count'];
                    } else {
                        $ArrRetunr[$currency]['declined_count'] = $value1['declined_count'];
                    }
                    if (isset($ArrRetunr[$currency]['declined_amount'])) {
                        $ArrRetunr[$currency]['declined_amount'] += $value1['declined_amount'];
                    } else {
                        $ArrRetunr[$currency]['declined_amount'] = $value1['declined_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['declined_percentage'])) {
                        $ArrRetunr[$currency]['declined_percentage'] += $value1['declined_percentage'];
                    } else {
                        $ArrRetunr[$currency]['declined_percentage'] = $value1['declined_percentage'];
                    }

                    if (isset($ArrRetunr[$currency]['chargebacks_count'])) {
                        $ArrRetunr[$currency]['chargebacks_count'] += $value1['chargebacks_count'];
                    } else {
                        $ArrRetunr[$currency]['chargebacks_count'] = $value1['chargebacks_count'];
                    }
                    if (isset($ArrRetunr[$currency]['chargebacks_amount'])) {
                        $ArrRetunr[$currency]['chargebacks_amount'] += $value1['chargebacks_amount'];
                    } else {
                        $ArrRetunr[$currency]['chargebacks_amount'] = $value1['chargebacks_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['chargebacks_percentage'])) {
                        $ArrRetunr[$currency]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                    } else {
                        $ArrRetunr[$currency]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['refund_count'])) {
                        $ArrRetunr[$currency]['refund_count'] += $value1['refund_count'];
                    } else {
                        $ArrRetunr[$currency]['refund_count'] = $value1['refund_count'];
                    }
                    if (isset($ArrRetunr[$currency]['refund_amount'])) {
                        $ArrRetunr[$currency]['refund_amount'] += $value1['refund_amount'];
                    } else {
                        $ArrRetunr[$currency]['refund_amount'] = $value1['refund_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['refund_percentage'])) {
                        $ArrRetunr[$currency]['refund_percentage'] += $value1['refund_percentage'];
                    } else {
                        $ArrRetunr[$currency]['refund_percentage'] = $value1['refund_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['flagged_count'])) {
                        $ArrRetunr[$currency]['flagged_count'] += $value1['flagged_count'];
                    } else {
                        $ArrRetunr[$currency]['flagged_count'] = $value1['flagged_count'];
                    }
                    if (isset($ArrRetunr[$currency]['flagged_amount'])) {
                        $ArrRetunr[$currency]['flagged_amount'] += $value1['flagged_amount'];
                    } else {
                        $ArrRetunr[$currency]['flagged_amount'] = $value1['flagged_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['flagged_percentage'])) {
                        $ArrRetunr[$currency]['flagged_percentage'] += $value1['flagged_percentage'];
                    } else {
                        $ArrRetunr[$currency]['flagged_percentage'] = $value1['flagged_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['retrieval_count'])) {
                        $ArrRetunr[$currency]['retrieval_count'] += $value1['retrieval_count'];
                    } else {
                        $ArrRetunr[$currency]['retrieval_count'] = $value1['retrieval_count'];
                    }
                    if (isset($ArrRetunr[$currency]['retrieval_amount'])) {
                        $ArrRetunr[$currency]['retrieval_amount'] += $value1['retrieval_amount'];
                    } else {
                        $ArrRetunr[$currency]['retrieval_amount'] = $value1['retrieval_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['retrieval_percentage'])) {
                        $ArrRetunr[$currency]['retrieval_percentage'] += $value1['retrieval_percentage'];
                    } else {
                        $ArrRetunr[$currency]['retrieval_percentage'] = $value1['retrieval_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['block_count'])) {
                        $ArrRetunr[$currency]['block_count'] += $value1['block_count'];
                    } else {
                        $ArrRetunr[$currency]['block_count'] = $value1['block_count'];
                    }
                    if (isset($ArrRetunr[$currency]['block_amount'])) {
                        $ArrRetunr[$currency]['block_amount'] += $value1['block_amount'];
                    } else {
                        $ArrRetunr[$currency]['block_amount'] = $value1['block_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['block_percentage'])) {
                        $ArrRetunr[$currency]['block_percentage'] += $value1['block_percentage'];
                    } else {
                        $ArrRetunr[$currency]['block_percentage'] = $value1['block_percentage'];
                    }
                }
            }
        }
        return $ArrRetunr;
    }

    public function getTodaysRecordForBankMerchantVolume($input, $userWithMids)
    {
        $start_date = "";
        $end_date = "";
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];
        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));
        }
        if ((!isset($_GET['for']) && !isset($_GET['end_date'])) || (isset($_GET['for']) && $_GET['for'] == 'Daily')) {
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
        }
        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $start_date = date('Y-m-d 00:00:00', strtotime('-6 days'));
            $end_date = date('Y-m-d 23:59:59');
        }
        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $start_date = date('Y-m-d 23:59:59', strtotime('-30 days'));
            $end_date = date('Y-m-d 00:00:00');
        }

        $today_chargeback_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("SUM(IF(transactions.chargebacks = '1', 1, 0)) AS chargebacks_count"),
            DB::raw("SUM(IF(transactions.chargebacks = '1', transactions.amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // ->where('transactions.status', '1')->where('transactions.chargebacks', '1');
        $today_chargeback_array = $today_chargeback_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_chargeback_array = $today_chargeback_array->where('transactions.chargebacks_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_chargeback_array = $today_chargeback_array->where('transactions.chargebacks_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_chargeback_array = $today_chargeback_array->whereIn('user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_chargeback_array = $today_chargeback_array->where('user_id', $input['user_id']);
        }

        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $today_chargeback_array = $today_chargeback_array->whereIn('transactions.user_id', $userWithMids['user_id']);
        } else {
            $today_chargeback_array = $today_chargeback_array->where('transactions.user_id', false);
        }
        if (isset($userWithMids['mid']) && !empty($userWithMids['mid'])) {
            $today_chargeback_array = $today_chargeback_array->whereIn('transactions.payment_gateway_id', $userWithMids['mid']);
        } else {
            $today_chargeback_array = $today_chargeback_array->where('transactions.payment_gateway_id', false);
        }

        if (isset($input['currency']) && $input['currency'] != null) {
            $today_chargeback_array = $today_chargeback_array->where('currency', $input['currency']);
        }

        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_chargeback_array = $today_chargeback_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_chargeback_array = $today_chargeback_array->groupBy('transactions.currency');
        }
        if (isset($input['chargebacks_per']) && !empty($input['chargebacks_per'])) {
            $today_chargeback_array = $today_chargeback_array->having('chargebacks_percentage', '>=', $input['chargebacks_per']);
        }
        $today_chargeback_array = $today_chargeback_array->orderBy('chargebacks_amount', 'DESC');

        $today_refund_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("SUM(IF(transactions.refund = '1', 1, 0)) AS refund_count"),
            DB::raw("SUM(IF(transactions.refund = '1', transactions.amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // $today_refund_array = $today_refund_array->where('transactions.status', '1')->where('transactions.refund', '1');
        $today_refund_array = $today_refund_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_refund_array = $today_refund_array->where('transactions.refund_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_refund_array = $today_refund_array->where('transactions.refund_date', '<=', $end_date);
        }
        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $today_refund_array = $today_refund_array->whereIn('transactions.user_id', $userWithMids['user_id']);
        } else {
            $today_refund_array = $today_refund_array->where('transactions.user_id', false);
        }
        if (isset($userWithMids['mid']) && !empty($userWithMids['mid'])) {
            $today_refund_array = $today_refund_array->whereIn('transactions.payment_gateway_id', $userWithMids['mid']);
        } else {
            $today_refund_array = $today_refund_array->where('transactions.payment_gateway_id', false);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_refund_array = $today_refund_array->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_refund_array = $today_refund_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_refund_array = $today_refund_array->groupBy('transactions.currency');
        }
        if (isset($input['refund_per']) && $input['refund_per'] != null) {
            $today_refund_array = $today_refund_array->having('refund_percentage', '>', $input['refund_per']);
        }
        $today_refund_array = $today_refund_array->orderBy('refund_amount', 'DESC');

        $today_flagged_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("SUM(IF(transactions.is_flagged = '1', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.is_flagged = '1', transactions.amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // ->where('transactions.status', '1')->where('transactions.is_flagged', '1');
        $today_flagged_array = $today_flagged_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_flagged_array = $today_flagged_array->where('transactions.flagged_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_flagged_array = $today_flagged_array->where('transactions.flagged_date', '<=', $end_date);
        }
        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $today_flagged_array = $today_flagged_array->whereIn('transactions.user_id', $userWithMids['user_id']);
        } else {
            $today_flagged_array = $today_flagged_array->where('transactions.user_id', false);
        }
        if (isset($userWithMids['mid']) && !empty($userWithMids['mid'])) {
            $today_flagged_array = $today_flagged_array->whereIn('transactions.payment_gateway_id', $userWithMids['mid']);
        } else {
            $today_flagged_array = $today_flagged_array->where('transactions.payment_gateway_id', false);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_flagged_array = $today_flagged_array->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_flagged_array = $today_flagged_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_flagged_array = $today_flagged_array->groupBy('transactions.currency');
        }
        if (isset($input['suspicious_per']) && $input['suspicious_per'] != null) {
            $today_flagged_array = $today_flagged_array->having('flagged_percentage', '>', $input['suspicious_per']);
        }
        $today_flagged_array = $today_flagged_array->orderBy('flagged_amount', 'DESC');

        $today_retrieval_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS success_amount_in_usd"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        );
        // ->where('transactions.status', '1')->where('transactions.is_flagged', '1');
        $today_retrieval_array = $today_retrieval_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_retrieval_array = $today_retrieval_array->where('transactions.retrieval_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_retrieval_array = $today_retrieval_array->where('transactions.retrieval_date', '<=', $end_date);
        }
        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $today_retrieval_array = $today_retrieval_array->whereIn('transactions.user_id', $userWithMids['user_id']);
        } else {
            $today_retrieval_array = $today_retrieval_array->where('transactions.user_id', false);
        }
        if (isset($userWithMids['mid']) && !empty($userWithMids['mid'])) {
            $today_retrieval_array = $today_retrieval_array->whereIn('transactions.payment_gateway_id', $userWithMids['mid']);
        } else {
            $today_retrieval_array = $today_retrieval_array->where('transactions.payment_gateway_id', false);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_retrieval_array = $today_retrieval_array->where('currency', $input['currency']);
        }
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_retrieval_array = $today_retrieval_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_retrieval_array = $today_retrieval_array->groupBy('transactions.currency');
        }
        if (isset($input['retrieval_per']) && $input['retrieval_per'] != null) {
            $today_retrieval_array = $today_retrieval_array->having('retrieval_percentage', '>', $input['retrieval_per']);
        }
        $today_retrieval_array = $today_retrieval_array->orderBy('retrieval_amount', 'DESC');

        $today_success_array = static::select(
            'transactions.currency',
            'transactions.user_id',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) AS success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0)) AS success_amount"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount_in_usd, 0.00)) AS success_amount_in_usd"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) AS declined_count"),
            DB::raw("SUM(IF(transactions.status = '0', transactions.amount, 0)) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage")
        );
        $today_success_array = $today_success_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_success_array = $today_success_array->where('transactions.created_at', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_success_array = $today_success_array->where('transactions.created_at', '<=', $end_date);
        }
        if (isset($userWithMids['user_id']) && !empty($userWithMids['user_id'])) {
            $today_success_array = $today_success_array->whereIn('transactions.user_id', $userWithMids['user_id']);
        } else {
            $today_success_array = $today_success_array->where('transactions.user_id', false);
        }
        if (isset($userWithMids['mid']) && !empty($userWithMids['mid'])) {
            $today_success_array = $today_success_array->whereIn('transactions.payment_gateway_id', $userWithMids['mid']);
        } else {
            $today_success_array = $today_success_array->where('transactions.payment_gateway_id', false);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_success_array = $today_success_array->where('currency', $input['currency']);
        }
        $today_success_array = $today_success_array->union($today_chargeback_array);
        $today_success_array = $today_success_array->union($today_refund_array);
        $today_success_array = $today_success_array->union($today_flagged_array);
        $today_success_array = $today_success_array->union($today_retrieval_array);
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $today_success_array = $today_success_array->groupBy('transactions.user_id', 'transactions.currency');
        } else {
            $today_success_array = $today_success_array->groupBy('transactions.currency');
        }
        if (isset($input['success_per']) && !empty($input['success_per'])) {
            $today_success_array = $today_success_array->having('success_percentage', '>=', $input['success_per']);
        }
        if (isset($input['decline_per']) && $input['decline_per'] != null) {
            $today_success_array = $today_success_array->having('declined_percentage', '>', $input['decline_per']);
        }
        if (isset($input['block_per']) && $input['block_per'] != null) {
            $today_success_array = $today_success_array->having('block_percentage', '>', $input['block_per']);
        }
        $today_success_array = $today_success_array->orderBy('success_amount', 'DESC');

        $data = $today_success_array->get();
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            $data = $data->groupBy('user_id', 'currency');
        } else {
            $data = $data->groupBy('currency');
        }

        $ArrRetunr = [];
        if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
            foreach ($data as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $user_id = $value1['user_id'];
                    $currency = $value1['currency'];
                    $ArrRetunr[$user_id][$currency]['currency'] = $currency;
                    if (isset($ArrRetunr[$user_id][$currency]['success_count'])) {
                        $ArrRetunr[$user_id][$currency]['success_count'] += $value1['success_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_count'] = $value1['success_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['success_amount'])) {
                        $ArrRetunr[$user_id][$currency]['success_amount'] += $value1['success_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_amount'] = $value1['success_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['success_amount_in_usd'])) {
                        $ArrRetunr[$user_id][$currency]['success_amount_in_usd'] += $value1['success_amount_in_usd'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_amount_in_usd'] = $value1['success_amount_in_usd'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['success_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['success_percentage'] += $value1['success_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['success_percentage'] = $value1['success_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['declined_count'])) {
                        $ArrRetunr[$user_id][$currency]['declined_count'] += $value1['declined_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['declined_count'] = $value1['declined_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['declined_amount'])) {
                        $ArrRetunr[$user_id][$currency]['declined_amount'] += $value1['declined_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['declined_amount'] = $value1['declined_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['declined_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['declined_percentage'] += $value1['declined_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['declined_percentage'] = $value1['declined_percentage'];
                    }

                    if (isset($ArrRetunr[$user_id][$currency]['chargebacks_count'])) {
                        $ArrRetunr[$user_id][$currency]['chargebacks_count'] += $value1['chargebacks_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['chargebacks_count'] = $value1['chargebacks_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['chargebacks_amount'])) {
                        $ArrRetunr[$user_id][$currency]['chargebacks_amount'] += $value1['chargebacks_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['chargebacks_amount'] = $value1['chargebacks_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['chargebacks_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['refund_count'])) {
                        $ArrRetunr[$user_id][$currency]['refund_count'] += $value1['refund_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['refund_count'] = $value1['refund_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['refund_amount'])) {
                        $ArrRetunr[$user_id][$currency]['refund_amount'] += $value1['refund_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['refund_amount'] = $value1['refund_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['refund_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['refund_percentage'] += $value1['refund_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['refund_percentage'] = $value1['refund_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['flagged_count'])) {
                        $ArrRetunr[$user_id][$currency]['flagged_count'] += $value1['flagged_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['flagged_count'] = $value1['flagged_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['flagged_amount'])) {
                        $ArrRetunr[$user_id][$currency]['flagged_amount'] += $value1['flagged_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['flagged_amount'] = $value1['flagged_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['flagged_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['flagged_percentage'] += $value1['flagged_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['flagged_percentage'] = $value1['flagged_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['retrieval_count'])) {
                        $ArrRetunr[$user_id][$currency]['retrieval_count'] += $value1['retrieval_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['retrieval_count'] = $value1['retrieval_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['retrieval_amount'])) {
                        $ArrRetunr[$user_id][$currency]['retrieval_amount'] += $value1['retrieval_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['retrieval_amount'] = $value1['retrieval_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['retrieval_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['retrieval_percentage'] += $value1['retrieval_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['retrieval_percentage'] = $value1['retrieval_percentage'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['block_count'])) {
                        $ArrRetunr[$user_id][$currency]['block_count'] += $value1['block_count'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['block_count'] = $value1['block_count'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['block_amount'])) {
                        $ArrRetunr[$user_id][$currency]['block_amount'] += $value1['block_amount'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['block_amount'] = $value1['block_amount'];
                    }
                    if (isset($ArrRetunr[$user_id][$currency]['block_percentage'])) {
                        $ArrRetunr[$user_id][$currency]['block_percentage'] += $value1['block_percentage'];
                    } else {
                        $ArrRetunr[$user_id][$currency]['block_percentage'] = $value1['block_percentage'];
                    }
                }
            }
        } else {
            foreach ($data as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $currency = $value1['currency'];
                    $ArrRetunr[$currency]['currency'] = $currency;
                    if (isset($ArrRetunr[$currency]['success_count'])) {
                        $ArrRetunr[$currency]['success_count'] += $value1['success_count'];
                    } else {
                        $ArrRetunr[$currency]['success_count'] = $value1['success_count'];
                    }
                    if (isset($ArrRetunr[$currency]['success_amount'])) {
                        $ArrRetunr[$currency]['success_amount'] += $value1['success_amount'];
                    } else {
                        $ArrRetunr[$currency]['success_amount'] = $value1['success_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['success_amount_in_usd'])) {
                        $ArrRetunr[$currency]['success_amount_in_usd'] += $value1['success_amount_in_usd'];
                    } else {
                        $ArrRetunr[$currency]['success_amount_in_usd'] = $value1['success_amount_in_usd'];
                    }
                    if (isset($ArrRetunr[$currency]['success_percentage'])) {
                        $ArrRetunr[$currency]['success_percentage'] += $value1['success_percentage'];
                    } else {
                        $ArrRetunr[$currency]['success_percentage'] = $value1['success_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['declined_count'])) {
                        $ArrRetunr[$currency]['declined_count'] += $value1['declined_count'];
                    } else {
                        $ArrRetunr[$currency]['declined_count'] = $value1['declined_count'];
                    }
                    if (isset($ArrRetunr[$currency]['declined_amount'])) {
                        $ArrRetunr[$currency]['declined_amount'] += $value1['declined_amount'];
                    } else {
                        $ArrRetunr[$currency]['declined_amount'] = $value1['declined_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['declined_percentage'])) {
                        $ArrRetunr[$currency]['declined_percentage'] += $value1['declined_percentage'];
                    } else {
                        $ArrRetunr[$currency]['declined_percentage'] = $value1['declined_percentage'];
                    }

                    if (isset($ArrRetunr[$currency]['chargebacks_count'])) {
                        $ArrRetunr[$currency]['chargebacks_count'] += $value1['chargebacks_count'];
                    } else {
                        $ArrRetunr[$currency]['chargebacks_count'] = $value1['chargebacks_count'];
                    }
                    if (isset($ArrRetunr[$currency]['chargebacks_amount'])) {
                        $ArrRetunr[$currency]['chargebacks_amount'] += $value1['chargebacks_amount'];
                    } else {
                        $ArrRetunr[$currency]['chargebacks_amount'] = $value1['chargebacks_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['chargebacks_percentage'])) {
                        $ArrRetunr[$currency]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                    } else {
                        $ArrRetunr[$currency]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['refund_count'])) {
                        $ArrRetunr[$currency]['refund_count'] += $value1['refund_count'];
                    } else {
                        $ArrRetunr[$currency]['refund_count'] = $value1['refund_count'];
                    }
                    if (isset($ArrRetunr[$currency]['refund_amount'])) {
                        $ArrRetunr[$currency]['refund_amount'] += $value1['refund_amount'];
                    } else {
                        $ArrRetunr[$currency]['refund_amount'] = $value1['refund_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['refund_percentage'])) {
                        $ArrRetunr[$currency]['refund_percentage'] += $value1['refund_percentage'];
                    } else {
                        $ArrRetunr[$currency]['refund_percentage'] = $value1['refund_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['flagged_count'])) {
                        $ArrRetunr[$currency]['flagged_count'] += $value1['flagged_count'];
                    } else {
                        $ArrRetunr[$currency]['flagged_count'] = $value1['flagged_count'];
                    }
                    if (isset($ArrRetunr[$currency]['flagged_amount'])) {
                        $ArrRetunr[$currency]['flagged_amount'] += $value1['flagged_amount'];
                    } else {
                        $ArrRetunr[$currency]['flagged_amount'] = $value1['flagged_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['flagged_percentage'])) {
                        $ArrRetunr[$currency]['flagged_percentage'] += $value1['flagged_percentage'];
                    } else {
                        $ArrRetunr[$currency]['flagged_percentage'] = $value1['flagged_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['retrieval_count'])) {
                        $ArrRetunr[$currency]['retrieval_count'] += $value1['retrieval_count'];
                    } else {
                        $ArrRetunr[$currency]['retrieval_count'] = $value1['retrieval_count'];
                    }
                    if (isset($ArrRetunr[$currency]['retrieval_amount'])) {
                        $ArrRetunr[$currency]['retrieval_amount'] += $value1['retrieval_amount'];
                    } else {
                        $ArrRetunr[$currency]['retrieval_amount'] = $value1['retrieval_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['retrieval_percentage'])) {
                        $ArrRetunr[$currency]['retrieval_percentage'] += $value1['retrieval_percentage'];
                    } else {
                        $ArrRetunr[$currency]['retrieval_percentage'] = $value1['retrieval_percentage'];
                    }
                    if (isset($ArrRetunr[$currency]['block_count'])) {
                        $ArrRetunr[$currency]['block_count'] += $value1['block_count'];
                    } else {
                        $ArrRetunr[$currency]['block_count'] = $value1['block_count'];
                    }
                    if (isset($ArrRetunr[$currency]['block_amount'])) {
                        $ArrRetunr[$currency]['block_amount'] += $value1['block_amount'];
                    } else {
                        $ArrRetunr[$currency]['block_amount'] = $value1['block_amount'];
                    }
                    if (isset($ArrRetunr[$currency]['block_percentage'])) {
                        $ArrRetunr[$currency]['block_percentage'] += $value1['block_percentage'];
                    } else {
                        $ArrRetunr[$currency]['block_percentage'] = $value1['block_percentage'];
                    }
                }
            }
        }
        return $ArrRetunr;
    }

    public function getReasonReportData($input)
    {

        $data = DB::table("middetails as mid")->select([
            'mid.id',
            'mid.bank_name',
            't.status as txn_status',
            't.card_type',
            't.currency',
            \DB::raw('count(t.id) as transaction_count'),
            "t.reason"
        ])
            ->leftjoin("transactions as t", "mid.id", "=", "t.payment_gateway_id");
        // dd($input);
        $data = $data->leftjoin("applications as a", "a.user_id", "=", "t.user_id");
        if (isset($input['start_date'])) {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])));
        } else {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '>=', date('Y-m-d 00:00:00'));
        }
        if (isset($input['end_date']) && !empty($input['end_date'])) {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])));
        }
        if (isset($input['user_id']) && !empty($input['user_id'])) {
            $data->where('t.user_id', $input['user_id']);
        }
        if (isset($input['payment_gateway_id']) && !empty($input['payment_gateway_id'])) {
            $data->where('mid.id', $input['payment_gateway_id']);
        }
        if (isset($input['currency']) && !empty($input['currency'])) {
            $data->where('t.currency', $input['currency']);
        }

        $data = $data->groupBy('t.currency', 't.card_type', 't.reason', 'mid.id')
            ->whereNotIn('mid.id', [1, 2])
            ->orderBy('mid.id')
            ->get();
        // dd($data);
        return $data;
    }

    public function getMerchantReasonReportData($input)
    {
        if (isset($input['is_export']) && $input['is_export'] == '1') {
            $select = [
                'a.business_name as merchant_name',
                'mid.bank_name',
                "transactions.reason",
                \DB::raw('count(transactions.id) as transaction_count')
            ];
        } else {
            $select = [
                'a.business_name as merchant_name',
                'mid.bank_name',
                "transactions.reason",
                \DB::raw('count(transactions.id) as transaction_count'),
                'transactions.status as txn_status'
            ];
        }

        $data = static::select($select)
            ->join("middetails as mid", "transactions.payment_gateway_id", "=", "mid.id");
        ;

        $data = $data->leftjoin("applications as a", "a.user_id", "=", "transactions.user_id");

        if (isset($input['start_date'])) {
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])));
        } else {
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '>=', date('Y-m-d 00:00:00'));
        }

        if (isset($input['end_date']) && !empty($input['end_date'])) {
            $data = $data->where(DB::raw('DATE(transactions.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])));
        }

        if (isset($input['user_id']) && !empty($input['user_id'])) {
            $data->where('transactions.user_id', $input['user_id']);
        }

        $data = $data->groupBy('transactions.reason', 'mid.bank_name', 'transactions.user_id')
            ->whereNotIn('mid.id', [1, 2])
            ->orderBy('transactions.user_id')
            ->get();
        return $data;
    }

    public function getMerchantTxnApprovalReportData($input)
    {

        $data = DB::table("middetails as mid")->select([
            'mid.id',
            'mid.bank_name',
            't.card_type',
            'a.business_name',
            \DB::raw("SUM(IF(t.status = '1', 1, 0)) AS success_count"),
            \DB::raw("SUM(IF(t.status = '0', 1, 0)) AS declined_count"),
            DB::raw("(SUM(IF(t.status = '1', 1, 0)*100)/SUM(IF(t.status = '1' OR t.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("(SUM(IF(t.status = '0', 1, 0)*100)/SUM(IF(t.status = '1' OR t.status = '0', 1, 0))) AS declined_percentage"),
            //"t.reason"
        ])
            ->leftjoin("transactions as t", "mid.id", "=", "t.payment_gateway_id");

        $data = $data->leftjoin("applications as a", "a.user_id", "=", "t.user_id");
        if (isset($input['start_date'])) {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])));
        } else {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '>=', date('Y-m-d 00:00:00'));
        }
        if (isset($input['end_date']) && !empty($input['end_date'])) {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])));
        }
        if (isset($input['user_id']) && !empty($input['user_id'])) {
            $data->where('t.user_id', $input['user_id']);
        }
        if (isset($input['payment_gateway_id']) && !empty($input['payment_gateway_id'])) {
            $data->where('mid.id', $input['payment_gateway_id']);
        }
        if (isset($input['card_type']) && !empty($input['card_type'])) {
            $data->where('t.card_type', $input['card_type']);
        }
        if (isset($input['success_percentage']) && !empty($input['success_percentage'])) {
            $data->having('success_percentage', $input['success_percentage_operator'], $input['success_percentage']);
        }
        if (isset($input['declined_percentage']) && !empty($input['declined_percentage'])) {
            $data->having('declined_percentage', $input['declined_percentage_operator'], $input['declined_percentage']);
        }
        // $data = $data->where('t.status', '1');
        $data = $data->groupBy('t.card_type', 't.user_id', 'mid.id');


        $data = $data->whereNotIn('mid.id', [1, 2])
            ->orderBy('mid.id')
            ->get();
        //dd($data);
        //$data = $data->orderBy('mid.id');
        //paginate($input['noList']);
        //dd($data);
        return $data;
    }

    public function getCountryWiseTxnReportData($input)
    {

        $data = DB::table("middetails as mid")->select([
            'mid.id',
            'mid.bank_name',
            't.card_type',
            't.country',
            \DB::raw("CONCAT(countries.name, ' (', t.country, ')') AS country_name"),
            \DB::raw("SUM(IF(t.status = '1', 1, 0)) AS success_count"),
            \DB::raw("SUM(IF(t.status = '0', 1, 0)) AS declined_count"),
            DB::raw("(SUM(IF(t.status = '1', 1, 0)*100)/SUM(IF(t.status = '1' OR t.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("(SUM(IF(t.status = '0', 1, 0)*100)/SUM(IF(t.status = '1' OR t.status = '0', 1, 0))) AS declined_percentage"),
            //"t.reason"
        ])
            ->leftjoin("transactions as t", "mid.id", "=", "t.payment_gateway_id")
            ->leftjoin('countries', function ($join) {
                $join->on('t.country', '=', 'countries.code')
                    ->orOn('t.country', '=', 'countries.iso3');
            });

        $data = $data->leftjoin("applications as a", "a.user_id", "=", "t.user_id");
        if (isset($input['start_date'])) {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])));
        } else {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '>=', date('Y-m-d 00:00:00'));
        }
        if (isset($input['end_date']) && !empty($input['end_date'])) {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])));
        }
        if (isset($input['user_id']) && !empty($input['user_id'])) {
            $data->where('t.user_id', $input['user_id']);
        }
        if (isset($input['country']) && !empty($input['country'])) {
            $data->where('t.country', $input['country']);
        }
        if (isset($input['payment_gateway_id']) && !empty($input['payment_gateway_id'])) {
            $data->where('mid.id', $input['payment_gateway_id']);
        }
        if (isset($input['card_type']) && !empty($input['card_type'])) {
            $data->where('t.card_type', $input['card_type']);
        }
        if (isset($input['success_percentage']) && !empty($input['success_percentage'])) {
            $data->having('success_percentage', $input['success_percentage_operator'], $input['success_percentage']);
        }
        if (isset($input['declined_percentage']) && !empty($input['declined_percentage'])) {
            $data->having('declined_percentage', $input['declined_percentage_operator'], $input['declined_percentage']);
        }
        // $data = $data->where('t.status', '1');
        $data = $data->groupBy('t.card_type', 't.country', 'mid.id')
            ->havingRaw('(success_count != 0 OR declined_count != 0)');


        $data = $data->whereNotIn('mid.id', [1, 2])
            ->orderBy('mid.id')
            ->get();
        //dd($data);
        //$data = $data->orderBy('mid.id');
        //paginate($input['noList']);
        //dd($data);
        return $data;
    }

    public function getAllTransactionCountry()
    {
        $data = DB::table("transactions as t")->select([

            't.country',
            'countries.name'
        ])
            ->leftjoin('countries', function ($join) {
                $join->on('t.country', '=', 'countries.code')
                    ->orOn('t.country', '=', 'countries.iso3');
            })
            ->groupBy('t.country')
            ->orderBy('countries.name')
            ->get();

        return $data->unique("name")->pluck("name", "country");
    }

    public function getMerchantCountryWiseTxnReportData($input)
    {

        $data = DB::table("middetails as mid")->select([
            'mid.id',
            'a.business_name',
            't.card_type',
            't.country',
            \DB::raw("CONCAT(countries.name, ' (', t.country, ')') AS country_name"),
            \DB::raw("SUM(IF(t.status = '1', 1, 0)) AS success_count"),
            \DB::raw("SUM(IF(t.status = '0', 1, 0)) AS declined_count"),
            DB::raw("(SUM(IF(t.status = '1', 1, 0)*100)/SUM(IF(t.status = '1' OR t.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("(SUM(IF(t.status = '0', 1, 0)*100)/SUM(IF(t.status = '1' OR t.status = '0', 1, 0))) AS declined_percentage"),
            //"t.reason"
        ])
            ->leftjoin("transactions as t", "mid.id", "=", "t.payment_gateway_id")
            ->leftjoin('countries', function ($join) {
                $join->on('t.country', '=', 'countries.code')
                    ->orOn('t.country', '=', 'countries.iso3');
            });

        $data = $data->leftjoin("applications as a", "a.user_id", "=", "t.user_id");

        if (isset($input['start_date'])) {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '>=', date('Y-m-d', strtotime($input['start_date'])));
        } else {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '>=', date('Y-m-d 00:00:00'));
        }
        if (isset($input['end_date']) && !empty($input['end_date'])) {
            $data = $data->where(DB::raw('DATE(t.created_at)'), '<=', date('Y-m-d', strtotime($input['end_date'])));
        }
        if (isset($input['user_id']) && !empty($input['user_id'])) {
            $data->where('t.user_id', $input['user_id']);
        }
        if (isset($input['country']) && !empty($input['country'])) {
            $data->where('t.country', $input['country']);
        }
        if (isset($input['payment_gateway_id']) && !empty($input['payment_gateway_id'])) {
            $data->where('mid.id', $input['payment_gateway_id']);
        }
        if (isset($input['card_type']) && !empty($input['card_type'])) {
            $data->where('t.card_type', $input['card_type']);
        }
        if (isset($input['success_percentage']) && !empty($input['success_percentage'])) {
            $data->having('success_percentage', $input['success_percentage_operator'], $input['success_percentage']);
        }
        if (isset($input['declined_percentage']) && !empty($input['declined_percentage'])) {
            $data->having('declined_percentage', $input['declined_percentage_operator'], $input['declined_percentage']);
        }
        // $data = $data->where('t.status', '1');
        $data = $data->groupBy('t.card_type', 't.country', 'a.business_name')
            ->havingRaw('(success_count != 0 OR declined_count != 0)');


        $data = $data->whereNotIn('mid.id', [1, 2])
            ->orderBy('a.business_name')
            ->get();

        return $data;
    }

    public function getMerchantDailyTransactionReport($input)
    {

        $data = static::select([
            'transactions.user_id',
            'applications.business_name',
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) as success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount_in_usd, 0.00)) AS success_amount_in_usd"),
            //DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) as declined_count"),
            //DB::raw("SUM(IF(transactions.status = '0' , transactions.amount,0.00 )) AS declined_amount"),
            //DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),

        ])->join('applications', 'applications.user_id', '=', 'transactions.user_id');

        if (isset($input['for']) && $input['for'] == 'twoDaysBack') {
            $twoDaysBack = date('d') - 2;
            $twoDaysBack = strlen($twoDaysBack) == 1 ? '0' . $twoDaysBack : $twoDaysBack;
            $data = $data->where('transactions.created_at', '>=', date('Y-m') . '-' . $twoDaysBack . ' 00:00:00')
                ->where('transactions.created_at', '<=', date('Y-m') . '-' . $twoDaysBack . ' 23:59:59');
        } elseif (isset($input['for']) && $input['for'] == 'Yesterday') {
            $prev_date = date('d') - 1;
            $prev_date = strlen($prev_date) == 1 ? '0' . $prev_date : $prev_date;

            $data = $data->where('transactions.created_at', '>=', date('Y-m') . '-' . $prev_date . ' 00:00:00')
                ->where('transactions.created_at', '<=', date('Y-m') . '-' . $prev_date . ' 23:59:59');
        } else {
            $data = $data->where('transactions.created_at', '>=', date('Y-m-d 00:00:00'))
                ->where('transactions.created_at', '<=', date('Y-m-d 23:59:59'));
        }


        $data = $data->whereNotIn('transactions.payment_gateway_id', [1, 2])
            ->groupBy('transactions.user_id')
            ->orderBy('declined_count', 'desc')->get();

        return $data;
    }

    public function getSummaryReportData($input)
    {
        $payment_gateway_id = (env('PAYMENT_GATEWAY_ID')) ? explode(",", env('PAYMENT_GATEWAY_ID')) : [1, 2];

        $start_date = "";
        $end_date = "";

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d 00:00:00', strtotime($input['start_date']));
            $end_date = date('Y-m-d 23:59:59', strtotime($input['end_date']));
        }
        if (isset($input['for']) && $input['for'] == 'Daily') {
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
        }
        if (isset($input['for']) && $input['for'] == 'Weekly') {
            $start_date = date('Y-m-d 00:00:00', strtotime('-6 days'));
            $end_date = date('Y-m-d 23:59:59');
        }
        if (isset($input['for']) && $input['for'] == 'Monthly') {
            $start_date = date('Y-m-d 00:00:00', strtotime('-30 days'));
            $end_date = date('Y-m-d 23:59:59');
        }
        $ChargebackSelectArray = [
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS amount_in_usd"),
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("SUM(IF(transactions.chargebacks = '1', 1, 0)) AS chargebacks_count"),
            DB::raw("SUM(IF(transactions.chargebacks = '1', transactions.amount, 0)) AS chargebacks_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0))*100/SUM(IF(transactions.status = '1', 1, 0))) AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage")
        ];
        if (isset($input['SelectFields']) && is_array($input['SelectFields']) && !empty($input['SelectFields'])) {
            $ChargebackSelectArray = array_merge($ChargebackSelectArray, $input['SelectFields']);
        }
        $today_chargeback_array = static::select($ChargebackSelectArray);
        if (isset($input['JoinTable']) && is_array($input['JoinTable']) && !empty($input['JoinTable'])) {
            $today_chargeback_array = $today_chargeback_array->join($input['JoinTable']['table'], $input['JoinTable']['condition'], $input['JoinTable']['conditionjoin']);
        }
        // $today_chargeback_array = $today_chargeback_array->where('transactions.status', '1')->where('transactions.chargebacks', '1');
        $today_chargeback_array = $today_chargeback_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_chargeback_array = $today_chargeback_array->where('transactions.chargebacks_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_chargeback_array = $today_chargeback_array->where('transactions.chargebacks_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_chargeback_array = $today_chargeback_array->whereIn('transactions.user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_chargeback_array = $today_chargeback_array->where('transactions.user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_chargeback_array = $today_chargeback_array->where('currency', $input['currency']);
        }
        if (isset($input['mid_type']) && $input['mid_type'] != null) {
            $today_chargeback_array = $today_chargeback_array->where('transactions.payment_gateway_id', $input['mid_type']);
        }
        if (isset($input['country']) && $input['country'] != null) {
            $today_chargeback_array = $today_chargeback_array->where('transactions.country', $input['country']);
        }
        if (isset($input['card_type']) && $input['card_type'] != null) {
            $today_chargeback_array = $today_chargeback_array->where('card_type', $input['card_type']);
        }

        if (isset($input['groupBy']) && !empty($input['groupBy'])) {
            $today_chargeback_array = $today_chargeback_array->groupBy($input['groupBy']);
        } else {
            if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
                $today_chargeback_array = $today_chargeback_array->groupBy('transactions.user_id', 'transactions.currency');
            } else {
                $today_chargeback_array = $today_chargeback_array->groupBy('transactions.currency');
            }
        }

        if (isset($input['chargebacks_per']) && !empty($input['chargebacks_per'])) {
            $today_chargeback_array = $today_chargeback_array->having('chargebacks_percentage', '>=', $input['chargebacks_per']);
        }
        if (isset($input['operation']) && $input['operation'] != null) {
            if (isset($input['percentage']) && $input['percentage'] != null) {
                if ($input['operation'] == 'greaterthan') {
                    $today_chargeback_array = $today_chargeback_array->having('chargebacks_percentage', '>=', $input['percentage']);
                }
                if ($input['operation'] == 'lessthan') {
                    $today_chargeback_array = $today_chargeback_array->having('chargebacks_percentage', '<', $input['percentage']);
                }
            }
        }

        $today_chargeback_array = $today_chargeback_array->orderBy('chargebacks_amount', 'DESC');


        $RefundSelectArray = [
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS amount_in_usd"),
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("SUM(IF(transactions.refund = '1', 1, 0)) AS refund_count"),
            DB::raw("SUM(IF(transactions.refund = '1', transactions.amount, 0)) AS refund_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove='0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        ];
        if (isset($input['SelectFields']) && is_array($input['SelectFields']) && !empty($input['SelectFields'])) {
            $RefundSelectArray = array_merge($RefundSelectArray, $input['SelectFields']);
        }
        $today_refund_array = static::select($RefundSelectArray);
        if (isset($input['JoinTable']) && is_array($input['JoinTable']) && !empty($input['JoinTable'])) {
            $today_refund_array = $today_refund_array->join($input['JoinTable']['table'], $input['JoinTable']['condition'], $input['JoinTable']['conditionjoin']);
        }
        // $today_refund_array = $today_refund_array->where('transactions.status', '1')->where('transactions.refund', '1');
        $today_refund_array = $today_refund_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_refund_array = $today_refund_array->where('transactions.refund_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_refund_array = $today_refund_array->where('transactions.refund_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_refund_array = $today_refund_array->whereIn('transactions.user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_refund_array = $today_refund_array->where('transactions.user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_refund_array = $today_refund_array->where('currency', $input['currency']);
        }
        if (isset($input['mid_type']) && $input['mid_type'] != null) {
            $today_refund_array = $today_refund_array->where('transactions.payment_gateway_id', $input['mid_type']);
        }
        if (isset($input['country']) && $input['country'] != null) {
            $today_refund_array = $today_refund_array->where('transactions.country', $input['country']);
        }
        if (isset($input['card_type']) && $input['card_type'] != null) {
            $today_refund_array = $today_refund_array->where('card_type', $input['card_type']);
        }

        if (isset($input['groupBy']) && !empty($input['groupBy'])) {
            $today_refund_array = $today_refund_array->groupBy($input['groupBy']);
        } else {
            if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
                $today_refund_array = $today_refund_array->groupBy('transactions.user_id', 'transactions.currency');
            } else {
                $today_refund_array = $today_refund_array->groupBy('transactions.currency');
            }
        }

        if (isset($input['refund_per']) && $input['refund_per'] != null) {
            $today_refund_array = $today_refund_array->having('refund_percentage', '>', $input['refund_per']);
        }
        if (isset($input['operation']) && $input['operation'] != null) {
            if (isset($input['percentage']) && $input['percentage'] != null) {
                if ($input['operation'] == 'greaterthan') {
                    $today_refund_array = $today_refund_array->having('refund_percentage', '>=', $input['percentage']);
                }
                if ($input['operation'] == 'lessthan') {
                    $today_refund_array = $today_refund_array->having('refund_percentage', '<', $input['percentage']);
                }
            }
        }
        $today_refund_array = $today_refund_array->orderBy('refund_amount', 'DESC');

        $FlaggedSelectArray = [
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS amount_in_usd"),
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("SUM(IF(transactions.is_flagged = '1', 1, 0)) AS flagged_count"),
            DB::raw("SUM(IF(transactions.is_flagged = '1', transactions.amount, 0)) AS flagged_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove= '0', 1, 0))/SUM(IF(transactions.status = '1', 1, 0))) AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        ];
        if (isset($input['SelectFields']) && is_array($input['SelectFields']) && !empty($input['SelectFields'])) {
            $FlaggedSelectArray = array_merge($FlaggedSelectArray, $input['SelectFields']);
        }
        $today_flagged_array = static::select($FlaggedSelectArray);
        if (isset($input['JoinTable']) && is_array($input['JoinTable']) && !empty($input['JoinTable'])) {
            $today_flagged_array = $today_flagged_array->join($input['JoinTable']['table'], $input['JoinTable']['condition'], $input['JoinTable']['conditionjoin']);
        }
        // $today_flagged_array = $today_flagged_array->where('transactions.status', '1')->where('transactions.is_flagged', '1');
        $today_flagged_array = $today_flagged_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_flagged_array = $today_flagged_array->where('transactions.flagged_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_flagged_array = $today_flagged_array->where('transactions.flagged_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_flagged_array = $today_flagged_array->whereIn('transactions.user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_flagged_array = $today_flagged_array->where('transactions.user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_flagged_array = $today_flagged_array->where('currency', $input['currency']);
        }
        if (isset($input['mid_type']) && $input['mid_type'] != null) {
            $today_flagged_array = $today_flagged_array->where('transactions.payment_gateway_id', $input['mid_type']);
        }
        if (isset($input['country']) && $input['country'] != null) {
            $today_flagged_array = $today_flagged_array->where('transactions.country', $input['country']);
        }
        if (isset($input['card_type']) && $input['card_type'] != null) {
            $today_flagged_array = $today_flagged_array->where('card_type', $input['card_type']);
        }

        if (isset($input['groupBy']) && !empty($input['groupBy'])) {
            $today_flagged_array = $today_flagged_array->groupBy($input['groupBy']);
        } else {
            if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
                $today_flagged_array = $today_flagged_array->groupBy('transactions.user_id', 'transactions.currency');
            } else {
                $today_flagged_array = $today_flagged_array->groupBy('transactions.currency');
            }
        }
        if (isset($input['suspicious_per']) && $input['suspicious_per'] != null) {
            $today_flagged_array = $today_flagged_array->having('flagged_percentage', '>', $input['suspicious_per']);
        }
        if (isset($input['operation']) && $input['operation'] != null) {
            if (isset($input['percentage']) && $input['percentage'] != null) {
                if ($input['operation'] == 'greaterthan') {
                    $today_flagged_array = $today_flagged_array->having('flagged_percentage', '>=', $input['percentage']);
                }
                if ($input['operation'] == 'lessthan') {
                    $today_flagged_array = $today_flagged_array->having('flagged_percentage', '<', $input['percentage']);
                }
            }
        }
        $today_flagged_array = $today_flagged_array->orderBy('flagged_amount', 'DESC');

        $RetrievalSelectArray = [
            'transactions.currency',
            'transactions.user_id',
            DB::raw("0 AS amount_in_usd"),
            DB::raw("0 AS success_count"),
            DB::raw("0 AS success_amount"),
            DB::raw("0 AS success_percentage"),
            DB::raw("0 AS declined_count"),
            DB::raw("0 AS declined_amount"),
            DB::raw("0 AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', 1, 0)) retrieval_count"),
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval  = '1' AND transactions.is_retrieval_remove= '0', amount, 0)) AS retrieval_amount"),
            DB::raw("(SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove= '0', 1, 0)*100)/SUM(IF(transactions.status = '1', 1, 0))) retrieval_percentage"),
            DB::raw("0 AS block_count"),
            DB::raw("0 AS block_amount"),
            DB::raw("0 AS block_percentage"),
        ];
        if (isset($input['SelectFields']) && is_array($input['SelectFields']) && !empty($input['SelectFields'])) {
            $RetrievalSelectArray = array_merge($RetrievalSelectArray, $input['SelectFields']);
        }
        $today_retrieval_array = static::select($RetrievalSelectArray, );
        if (isset($input['JoinTable']) && is_array($input['JoinTable']) && !empty($input['JoinTable'])) {
            $today_retrieval_array = $today_retrieval_array->join($input['JoinTable']['table'], $input['JoinTable']['condition'], $input['JoinTable']['conditionjoin']);
        }
        // $today_retrieval_array = $today_retrieval_array->where('transactions.status', '1')->where('transactions.is_flagged', '1');
        $today_retrieval_array = $today_retrieval_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_retrieval_array = $today_retrieval_array->where('transactions.retrieval_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_retrieval_array = $today_retrieval_array->where('transactions.retrieval_date', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_retrieval_array = $today_retrieval_array->whereIn('transactions.user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_retrieval_array = $today_retrieval_array->where('transactions.user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_retrieval_array = $today_retrieval_array->where('currency', $input['currency']);
        }
        if (isset($input['mid_type']) && $input['mid_type'] != null) {
            $today_retrieval_array = $today_retrieval_array->where('transactions.payment_gateway_id', $input['mid_type']);
        }
        if (isset($input['country']) && $input['country'] != null) {
            $today_retrieval_array = $today_retrieval_array->where('transactions.country', $input['country']);
        }
        if (isset($input['card_type']) && $input['card_type'] != null) {
            $today_retrieval_array = $today_retrieval_array->where('card_type', $input['card_type']);
        }

        if (isset($input['groupBy']) && !empty($input['groupBy'])) {
            $today_retrieval_array = $today_retrieval_array->groupBy($input['groupBy']);
        } else {
            if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
                $today_retrieval_array = $today_retrieval_array->groupBy('transactions.user_id', 'transactions.currency');
            } else {
                $today_retrieval_array = $today_retrieval_array->groupBy('transactions.currency');
            }
        }
        if (isset($input['retrieval_per']) && $input['retrieval_per'] != null) {
            $today_retrieval_array = $today_retrieval_array->having('retrieval_percentage', '>', $input['retrieval_per']);
        }
        if (isset($input['operation']) && $input['operation'] != null) {
            if (isset($input['percentage']) && $input['percentage'] != null) {
                if ($input['operation'] == 'greaterthan') {
                    $today_retrieval_array = $today_retrieval_array->having('retrieval_percentage', '>=', $input['percentage']);
                }
                if ($input['operation'] == 'lessthan') {
                    $today_retrieval_array = $today_retrieval_array->having('retrieval_percentage', '<', $input['percentage']);
                }
            }
        }

        $today_retrieval_array = $today_retrieval_array->orderBy('retrieval_amount', 'DESC');

        $SuccessSelectArray = [
            'transactions.currency',
            'transactions.user_id',
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount_in_usd, 0)) AS amount_in_usd"),
            DB::raw("SUM(IF(transactions.status = '1', 1, 0)) AS success_count"),
            DB::raw("SUM(IF(transactions.status = '1', transactions.amount, 0)) AS success_amount"),
            DB::raw("(SUM(IF(transactions.status = '1', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS success_percentage"),
            DB::raw("SUM(IF(transactions.status = '0', 1, 0)) AS declined_count"),
            DB::raw("SUM(IF(transactions.status = '0', transactions.amount, 0)) AS declined_amount"),
            DB::raw("(SUM(IF(transactions.status = '0', 1, 0)*100)/SUM(IF(transactions.status = '1' OR transactions.status = '0', 1, 0))) AS declined_percentage"),
            DB::raw("0 AS chargebacks_count"),
            DB::raw("0 AS chargebacks_amount"),
            DB::raw("0 AS chargebacks_percentage"),
            DB::raw("0 AS refund_count"),
            DB::raw("0 AS refund_amount"),
            DB::raw("0 AS refund_percentage"),
            DB::raw("0 AS flagged_count"),
            DB::raw("0 AS flagged_amount"),
            DB::raw("0 AS flagged_percentage"),
            DB::raw("0 AS retrieval_count"),
            DB::raw("0 AS retrieval_amount"),
            DB::raw("0 AS retrieval_percentage"),
            DB::raw("SUM(IF(transactions.status = '5', 1, 0)) AS block_count"),
            DB::raw("SUM(IF(transactions.status = '5', transactions.amount, 0.00)) AS block_amount"),
            DB::raw("(SUM(IF(transactions.status = '5', 1, 0))*100/COUNT(transactions.id)) AS block_percentage"),
        ];
        if (isset($input['SelectFields']) && is_array($input['SelectFields']) && !empty($input['SelectFields'])) {
            $SuccessSelectArray = array_merge($SuccessSelectArray, $input['SelectFields']);
        }
        $today_success_array = static::select($SuccessSelectArray, );
        if (isset($input['JoinTable']) && is_array($input['JoinTable']) && !empty($input['JoinTable'])) {
            $today_success_array = $today_success_array->join($input['JoinTable']['table'], $input['JoinTable']['condition'], $input['JoinTable']['conditionjoin']);
        }
        $today_success_array = $today_success_array->whereNotIn('transactions.payment_gateway_id', $payment_gateway_id);
        if (!empty($start_date)) {
            $today_success_array = $today_success_array->where('transactions.created_at', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $today_success_array = $today_success_array->where('transactions.created_at', '<=', $end_date);
        }
        if (isset($input['user_id']) && is_array($input['user_id']) && !empty($input['user_id'])) {
            $today_success_array = $today_success_array->whereIn('transactions.user_id', $input['user_id']);
        } else if (isset($input['user_id']) && $input['user_id'] != null) {
            $today_success_array = $today_success_array->where('transactions.user_id', $input['user_id']);
        }
        if (isset($input['currency']) && $input['currency'] != null) {
            $today_success_array = $today_success_array->where('currency', $input['currency']);
        }
        if (isset($input['mid_type']) && $input['mid_type'] != null) {
            $today_success_array = $today_success_array->where('transactions.payment_gateway_id', $input['mid_type']);
        }
        if (isset($input['country']) && $input['country'] != null) {
            $today_success_array = $today_success_array->where('transactions.country', $input['country']);
        }
        if (isset($input['card_type']) && $input['card_type'] != null) {
            $today_success_array = $today_success_array->where('card_type', $input['card_type']);
        }

        $today_success_array = $today_success_array->union($today_chargeback_array);
        $today_success_array = $today_success_array->union($today_refund_array);
        $today_success_array = $today_success_array->union($today_flagged_array);
        $today_success_array = $today_success_array->union($today_retrieval_array);
        if (isset($input['groupBy']) && !empty($input['groupBy'])) {
            $today_success_array = $today_success_array->groupBy($input['groupBy']);
        } else {
            if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
                $today_success_array = $today_success_array->groupBy('transactions.user_id', 'transactions.currency');
            } else {
                $today_success_array = $today_success_array->groupBy('transactions.currency');
            }
        }
        if (isset($input['success_per']) && !empty($input['success_per'])) {
            $today_success_array = $today_success_array->having('success_percentage', '>=', $input['success_per']);
        }
        if (isset($input['decline_per']) && $input['decline_per'] != null) {
            $today_success_array = $today_success_array->having('declined_percentage', '>', $input['decline_per']);
        }
        if (isset($input['block_per']) && $input['block_per'] != null) {
            $today_success_array = $today_success_array->having('block_percentage', '>', $input['block_per']);
        }

        if (isset($input['operation']) && $input['operation'] != null) {
            if (isset($input['percentage']) && $input['percentage'] != null) {
                if ($input['operation'] == 'greaterthan') {
                    $today_success_array = $today_success_array->having('success_percentage', '>=', $input['percentage']);
                    $today_success_array = $today_success_array->having('declined_percentage', '>=', $input['percentage']);
                    $today_success_array = $today_success_array->having('block_percentage', '>=', $input['percentage']);
                }
                if ($input['operation'] == 'lessthan') {
                    $today_success_array = $today_success_array->having('success_percentage', '<', $input['percentage']);
                    $today_success_array = $today_success_array->having('declined_percentage', '<', $input['percentage']);
                    $today_success_array = $today_success_array->having('block_percentage', '<', $input['percentage']);
                }
            }
        }

        $today_success_array = $today_success_array->orderBy('success_amount', 'DESC');

        $data = $today_success_array->get();
        if (isset($input['groupBy']) && !empty($input['groupBy'])) {
            $data = $data->groupBy($input['groupBy']);
        } else {
            if (isset($input['by_merchant']) && $input['by_merchant'] == 1) {
                $data = $data->groupBy('user_id', 'currency');
            } else {
                $data = $data->groupBy('currency');
            }
        }
        return $data;
    }

    public function PorcessSumarryData($typeofProcess = null, $DataOfProcess = array())
    {
        $ArrRetunr = [];
        if ($typeofProcess == 'CardTypeSumamry') {
            foreach ($DataOfProcess as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $GroupById = 0;
                    if (!empty($value1['card_type'])) {
                        $GroupById = $value1['card_type'];
                    }
                    $ArrRetunr[$GroupById]['card_type'] = $value1['card_type'];
                    if (isset($ArrRetunr[$GroupById]['success_count'])) {
                        $ArrRetunr[$GroupById]['success_count'] += $value1['success_count'];
                    } else {
                        $ArrRetunr[$GroupById]['success_count'] = $value1['success_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['success_amount'])) {
                        $ArrRetunr[$GroupById]['success_amount'] += $value1['success_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['success_amount'] = $value1['success_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['success_percentage'])) {
                        $ArrRetunr[$GroupById]['success_percentage'] += $value1['success_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['success_percentage'] = $value1['success_percentage'];
                    }
                    if (isset($ArrRetunr[$GroupById]['declined_count'])) {
                        $ArrRetunr[$GroupById]['declined_count'] += $value1['declined_count'];
                    } else {
                        $ArrRetunr[$GroupById]['declined_count'] = $value1['declined_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['declined_amount'])) {
                        $ArrRetunr[$GroupById]['declined_amount'] += $value1['declined_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['declined_amount'] = $value1['declined_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['declined_percentage'])) {
                        $ArrRetunr[$GroupById]['declined_percentage'] += $value1['declined_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['declined_percentage'] = $value1['declined_percentage'];
                    }

                    if (isset($ArrRetunr[$GroupById]['chargebacks_count'])) {
                        $ArrRetunr[$GroupById]['chargebacks_count'] += $value1['chargebacks_count'];
                    } else {
                        $ArrRetunr[$GroupById]['chargebacks_count'] = $value1['chargebacks_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['chargebacks_amount'])) {
                        $ArrRetunr[$GroupById]['chargebacks_amount'] += $value1['chargebacks_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['chargebacks_amount'] = $value1['chargebacks_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['chargebacks_percentage'])) {
                        $ArrRetunr[$GroupById]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                    }
                    if (isset($ArrRetunr[$GroupById]['refund_count'])) {
                        $ArrRetunr[$GroupById]['refund_count'] += $value1['refund_count'];
                    } else {
                        $ArrRetunr[$GroupById]['refund_count'] = $value1['refund_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['refund_amount'])) {
                        $ArrRetunr[$GroupById]['refund_amount'] += $value1['refund_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['refund_amount'] = $value1['refund_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['refund_percentage'])) {
                        $ArrRetunr[$GroupById]['refund_percentage'] += $value1['refund_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['refund_percentage'] = $value1['refund_percentage'];
                    }
                    if (isset($ArrRetunr[$GroupById]['flagged_count'])) {
                        $ArrRetunr[$GroupById]['flagged_count'] += $value1['flagged_count'];
                    } else {
                        $ArrRetunr[$GroupById]['flagged_count'] = $value1['flagged_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['flagged_amount'])) {
                        $ArrRetunr[$GroupById]['flagged_amount'] += $value1['flagged_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['flagged_amount'] = $value1['flagged_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['flagged_percentage'])) {
                        $ArrRetunr[$GroupById]['flagged_percentage'] += $value1['flagged_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['flagged_percentage'] = $value1['flagged_percentage'];
                    }
                    if (isset($ArrRetunr[$GroupById]['retrieval_count'])) {
                        $ArrRetunr[$GroupById]['retrieval_count'] += $value1['retrieval_count'];
                    } else {
                        $ArrRetunr[$GroupById]['retrieval_count'] = $value1['retrieval_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['retrieval_amount'])) {
                        $ArrRetunr[$GroupById]['retrieval_amount'] += $value1['retrieval_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['retrieval_amount'] = $value1['retrieval_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['retrieval_percentage'])) {
                        $ArrRetunr[$GroupById]['retrieval_percentage'] += $value1['retrieval_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['retrieval_percentage'] = $value1['retrieval_percentage'];
                    }
                    if (isset($ArrRetunr[$GroupById]['block_count'])) {
                        $ArrRetunr[$GroupById]['block_count'] += $value1['block_count'];
                    } else {
                        $ArrRetunr[$GroupById]['block_count'] = $value1['block_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['block_amount'])) {
                        $ArrRetunr[$GroupById]['block_amount'] += $value1['block_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['block_amount'] = $value1['block_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['block_percentage'])) {
                        $ArrRetunr[$GroupById]['block_percentage'] += $value1['block_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['block_percentage'] = $value1['block_percentage'];
                    }
                }
            }
        } else if ($typeofProcess == 'midSummary') {
            foreach ($DataOfProcess as $k => $v) {
                foreach ($v as $kk => $vv) {
                    foreach ($vv as $kks => $value1) {

                        $GroupById1 = $value1['payment_gateway_id'];
                        $GroupById2 = $value1['currency'];
                        $ArrRetunr[$GroupById1][$GroupById2]['bank_name'] = $value1['bank_name'];
                        $ArrRetunr[$GroupById1][$GroupById2]['payment_gateway_id'] = $value1['payment_gateway_id'];
                        $ArrRetunr[$GroupById1][$GroupById2]['currency'] = $value1['currency'];
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['amount_in_usd'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['amount_in_usd'] += $value1['amount_in_usd'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['amount_in_usd'] = $value1['amount_in_usd'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['success_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_count'] += $value1['success_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_count'] = $value1['success_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['success_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_amount'] += $value1['success_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_amount'] = $value1['success_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['success_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_percentage'] += $value1['success_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_percentage'] = $value1['success_percentage'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['declined_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_count'] += $value1['declined_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_count'] = $value1['declined_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['declined_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_amount'] += $value1['declined_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_amount'] = $value1['declined_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['declined_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_percentage'] += $value1['declined_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_percentage'] = $value1['declined_percentage'];
                        }

                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['chargebacks_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_count'] += $value1['chargebacks_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_count'] = $value1['chargebacks_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['chargebacks_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_amount'] += $value1['chargebacks_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_amount'] = $value1['chargebacks_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['chargebacks_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['refund_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_count'] += $value1['refund_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_count'] = $value1['refund_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['refund_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_amount'] += $value1['refund_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_amount'] = $value1['refund_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['refund_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_percentage'] += $value1['refund_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_percentage'] = $value1['refund_percentage'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['flagged_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_count'] += $value1['flagged_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_count'] = $value1['flagged_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['flagged_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_amount'] += $value1['flagged_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_amount'] = $value1['flagged_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['flagged_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_percentage'] += $value1['flagged_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_percentage'] = $value1['flagged_percentage'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['retrieval_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_count'] += $value1['retrieval_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_count'] = $value1['retrieval_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['retrieval_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_amount'] += $value1['retrieval_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_amount'] = $value1['retrieval_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['retrieval_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_percentage'] += $value1['retrieval_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_percentage'] = $value1['retrieval_percentage'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['block_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_count'] += $value1['block_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_count'] = $value1['block_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['block_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_amount'] += $value1['block_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_amount'] = $value1['block_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['block_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_percentage'] += $value1['block_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_percentage'] = $value1['block_percentage'];
                        }
                    }
                }
            }
        } else if ($typeofProcess == 'midSummaryForExcel') {
            foreach ($DataOfProcess as $key => $value) {
                foreach ($value as $k => $v) {
                    if (isset($v['payment_gateway_id'])) {
                        unset($v['payment_gateway_id']);
                        unset($v['amount_in_usd']);
                    }
                    $ArrRetunr[] = $v;
                }
            }
        } else if ($typeofProcess == 'CountrySummary') {
            foreach ($DataOfProcess as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $GroupById = $value1['country'];
                    $ArrRetunr[$GroupById]['country'] = $value1['country'];
                    if (isset($ArrRetunr[$GroupById]['success_count'])) {
                        $ArrRetunr[$GroupById]['success_count'] += $value1['success_count'];
                    } else {
                        $ArrRetunr[$GroupById]['success_count'] = $value1['success_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['success_amount'])) {
                        $ArrRetunr[$GroupById]['success_amount'] += $value1['success_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['success_amount'] = $value1['success_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['success_percentage'])) {
                        $ArrRetunr[$GroupById]['success_percentage'] += $value1['success_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['success_percentage'] = $value1['success_percentage'];
                    }
                    if (isset($ArrRetunr[$GroupById]['declined_count'])) {
                        $ArrRetunr[$GroupById]['declined_count'] += $value1['declined_count'];
                    } else {
                        $ArrRetunr[$GroupById]['declined_count'] = $value1['declined_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['declined_amount'])) {
                        $ArrRetunr[$GroupById]['declined_amount'] += $value1['declined_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['declined_amount'] = $value1['declined_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['declined_percentage'])) {
                        $ArrRetunr[$GroupById]['declined_percentage'] += $value1['declined_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['declined_percentage'] = $value1['declined_percentage'];
                    }

                    if (isset($ArrRetunr[$GroupById]['chargebacks_count'])) {
                        $ArrRetunr[$GroupById]['chargebacks_count'] += $value1['chargebacks_count'];
                    } else {
                        $ArrRetunr[$GroupById]['chargebacks_count'] = $value1['chargebacks_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['chargebacks_amount'])) {
                        $ArrRetunr[$GroupById]['chargebacks_amount'] += $value1['chargebacks_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['chargebacks_amount'] = $value1['chargebacks_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['chargebacks_percentage'])) {
                        $ArrRetunr[$GroupById]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                    }
                    if (isset($ArrRetunr[$GroupById]['refund_count'])) {
                        $ArrRetunr[$GroupById]['refund_count'] += $value1['refund_count'];
                    } else {
                        $ArrRetunr[$GroupById]['refund_count'] = $value1['refund_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['refund_amount'])) {
                        $ArrRetunr[$GroupById]['refund_amount'] += $value1['refund_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['refund_amount'] = $value1['refund_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['refund_percentage'])) {
                        $ArrRetunr[$GroupById]['refund_percentage'] += $value1['refund_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['refund_percentage'] = $value1['refund_percentage'];
                    }
                    if (isset($ArrRetunr[$GroupById]['flagged_count'])) {
                        $ArrRetunr[$GroupById]['flagged_count'] += $value1['flagged_count'];
                    } else {
                        $ArrRetunr[$GroupById]['flagged_count'] = $value1['flagged_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['flagged_amount'])) {
                        $ArrRetunr[$GroupById]['flagged_amount'] += $value1['flagged_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['flagged_amount'] = $value1['flagged_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['flagged_percentage'])) {
                        $ArrRetunr[$GroupById]['flagged_percentage'] += $value1['flagged_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['flagged_percentage'] = $value1['flagged_percentage'];
                    }
                    if (isset($ArrRetunr[$GroupById]['retrieval_count'])) {
                        $ArrRetunr[$GroupById]['retrieval_count'] += $value1['retrieval_count'];
                    } else {
                        $ArrRetunr[$GroupById]['retrieval_count'] = $value1['retrieval_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['retrieval_amount'])) {
                        $ArrRetunr[$GroupById]['retrieval_amount'] += $value1['retrieval_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['retrieval_amount'] = $value1['retrieval_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['retrieval_percentage'])) {
                        $ArrRetunr[$GroupById]['retrieval_percentage'] += $value1['retrieval_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['retrieval_percentage'] = $value1['retrieval_percentage'];
                    }
                    if (isset($ArrRetunr[$GroupById]['block_count'])) {
                        $ArrRetunr[$GroupById]['block_count'] += $value1['block_count'];
                    } else {
                        $ArrRetunr[$GroupById]['block_count'] = $value1['block_count'];
                    }
                    if (isset($ArrRetunr[$GroupById]['block_amount'])) {
                        $ArrRetunr[$GroupById]['block_amount'] += $value1['block_amount'];
                    } else {
                        $ArrRetunr[$GroupById]['block_amount'] = $value1['block_amount'];
                    }
                    if (isset($ArrRetunr[$GroupById]['block_percentage'])) {
                        $ArrRetunr[$GroupById]['block_percentage'] += $value1['block_percentage'];
                    } else {
                        $ArrRetunr[$GroupById]['block_percentage'] = $value1['block_percentage'];
                    }
                }
            }
        } else if ($typeofProcess == 'PaymentSsummary') {
            foreach ($DataOfProcess as $k => $v) {
                foreach ($v as $kk => $vv) {
                    foreach ($vv as $kks => $value1) {

                        $GroupById1 = $value1['user_id'];
                        $GroupById2 = $value1['currency'];
                        $ArrRetunr[$GroupById1][$GroupById2]['business_name'] = $value1['business_name'];
                        $ArrRetunr[$GroupById1][$GroupById2]['user_id'] = $value1['user_id'];
                        $ArrRetunr[$GroupById1][$GroupById2]['currency'] = $value1['currency'];
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['success_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_count'] += $value1['success_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_count'] = $value1['success_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['success_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_amount'] += $value1['success_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_amount'] = $value1['success_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['success_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_percentage'] += $value1['success_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['success_percentage'] = $value1['success_percentage'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['declined_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_count'] += $value1['declined_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_count'] = $value1['declined_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['declined_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_amount'] += $value1['declined_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_amount'] = $value1['declined_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['declined_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_percentage'] += $value1['declined_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['declined_percentage'] = $value1['declined_percentage'];
                        }

                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['chargebacks_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_count'] += $value1['chargebacks_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_count'] = $value1['chargebacks_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['chargebacks_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_amount'] += $value1['chargebacks_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_amount'] = $value1['chargebacks_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['chargebacks_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_percentage'] += $value1['chargebacks_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['chargebacks_percentage'] = $value1['chargebacks_percentage'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['refund_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_count'] += $value1['refund_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_count'] = $value1['refund_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['refund_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_amount'] += $value1['refund_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_amount'] = $value1['refund_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['refund_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_percentage'] += $value1['refund_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['refund_percentage'] = $value1['refund_percentage'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['flagged_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_count'] += $value1['flagged_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_count'] = $value1['flagged_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['flagged_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_amount'] += $value1['flagged_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_amount'] = $value1['flagged_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['flagged_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_percentage'] += $value1['flagged_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['flagged_percentage'] = $value1['flagged_percentage'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['retrieval_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_count'] += $value1['retrieval_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_count'] = $value1['retrieval_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['retrieval_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_amount'] += $value1['retrieval_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_amount'] = $value1['retrieval_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['retrieval_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_percentage'] += $value1['retrieval_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['retrieval_percentage'] = $value1['retrieval_percentage'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['block_count'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_count'] += $value1['block_count'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_count'] = $value1['block_count'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['block_amount'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_amount'] += $value1['block_amount'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_amount'] = $value1['block_amount'];
                        }
                        if (isset($ArrRetunr[$GroupById1][$GroupById2]['block_percentage'])) {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_percentage'] += $value1['block_percentage'];
                        } else {
                            $ArrRetunr[$GroupById1][$GroupById2]['block_percentage'] = $value1['block_percentage'];
                        }
                    }
                }
            }
        }
        return $ArrRetunr;
    }

    public function getRiskComplianceReportData($input)
    {
        $start_date = date('Y-m-d 00:00:00', strtotime('-30 days'));
        $end_date = date('Y-m-d 23:59:59');

        if ((isset($input['start_date']) && $input['start_date'] != '') && (isset($input['end_date']) && $input['end_date'] != '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
            $end_date = date('Y-m-d', strtotime($input['end_date']));
        } else if ((isset($input['start_date']) && $input['start_date'] != '') || (isset($input['end_date']) && $input['end_date'] == '')) {
            $start_date = date('Y-m-d', strtotime($input['start_date']));
        } else if ((isset($input['start_date']) && $input['start_date'] == '') || (isset($input['end_date']) && $input['end_date'] != '')) {
            $end_date = date('Y-m-d', strtotime($input['end_date']));
        }

        $data['success'] = DB::table('transactions')->select(
            DB::raw("SUM(IF(transactions.status = '1' , 1, 0)) success_count")
        )
            ->where('transactions.created_at', '>=', $start_date)
            ->where('transactions.created_at', '<=', $end_date)
            ->where('transactions.user_id', $input['user_id']);

        if (isset($input['payment_gateway_id'])) {
            $data['success'] = $data['success']->whereIn('payment_gateway_id', $input['payment_gateway_id']);
        }

        $data['success'] = $data['success']->first();

        $data['refund'] = DB::table('transactions')->select(
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.refund = '1' AND transactions.refund_remove = '0', 1, 0)) refund_count")
        )
            ->where('transactions.refund_date', '>=', $start_date)
            ->where('transactions.refund_date', '<=', $end_date)
            ->where('transactions.user_id', $input['user_id']);

        if (isset($input['payment_gateway_id'])) {
            $data['refund'] = $data['refund']->whereIn('payment_gateway_id', $input['payment_gateway_id']);
        }

        $data['refund'] = $data['refund']->first();

        $data['chargebacks'] = DB::table('transactions')->select(
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.chargebacks = '1' AND transactions.chargebacks_remove = '0', 1, 0)) chargebacks_count")
        )
            ->where('transactions.chargebacks_date', '>=', $start_date)
            ->where('transactions.chargebacks_date', '<=', $end_date)
            ->where('transactions.user_id', $input['user_id']);

        if (isset($input['payment_gateway_id'])) {
            $data['chargebacks'] = $data['chargebacks']->whereIn('payment_gateway_id', $input['payment_gateway_id']);
        }

        $data['chargebacks'] = $data['chargebacks']->first();

        $data['flagged'] = DB::table('transactions')->select(
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_flagged = '1' AND transactions.is_flagged_remove = '0', 1, 0)) flagged_count")
        )
            ->where('transactions.flagged_date', '>=', $start_date)
            ->where('transactions.flagged_date', '<=', $end_date)
            ->where('transactions.user_id', $input['user_id']);

        if (isset($input['payment_gateway_id'])) {
            $data['flagged'] = $data['flagged']->whereIn('payment_gateway_id', $input['payment_gateway_id']);
        }

        $data['flagged'] = $data['flagged']->first();


        $data['retrieval'] = DB::table('transactions')->select(
            DB::raw("SUM(IF(transactions.status = '1' AND transactions.is_retrieval = '1' AND transactions.is_retrieval_remove = '0', 1, 0)) retrieval_count")
        )
            ->where('transactions.retrieval_date', '>=', $start_date)
            ->where('transactions.retrieval_date', '<=', $end_date)
            ->where('transactions.user_id', $input['user_id']);

        if (isset($input['payment_gateway_id'])) {
            $data['retrieval'] = $data['retrieval']->whereIn('payment_gateway_id', $input['payment_gateway_id']);
        }

        $data['retrieval'] = $data['retrieval']->first();

        $data['chargebacks_percentage'] = $data['refund_percentage'] = $data['flagged_percentage'] = $data['retrieval_percentage'] = 0;
        if ($data['success']->success_count) {
            $data['chargebacks_percentage'] = round(($data['chargebacks']->chargebacks_count * 100) / $data['success']->success_count, 2);
            $data['refund_percentage'] = round(($data['refund']->refund_count * 100) / $data['success']->success_count, 2);
            $data['flagged_percentage'] = round(($data['flagged']->flagged_count * 100) / $data['success']->success_count, 2);
            $data['retrieval_percentage'] = round(($data['retrieval']->retrieval_count * 100) / $data['success']->success_count, 2);
        }

        if ($data['chargebacks_percentage'] > 3 || $data['refund_percentage'] > 10 || $data['flagged_percentage'] > 6 || $data['retrieval_percentage'] > 3) {
            $data['text'] = 'High';
            $data['color'] = 'danger';
            $data['percentage'] = '100';
        } elseif (($data['chargebacks_percentage'] > 1 && $data['chargebacks_percentage'] <= 3) || ($data['refund_percentage'] > 5 && $data['refund_percentage'] <= 10) || ($data['flagged_percentage'] > 2 && $data['flagged_percentage'] <= 6) || ($data['retrieval_percentage'] > 1 && $data['retrieval_percentage'] <= 3)) {
            $data['text'] = 'Mid';
            $data['color'] = 'warning';
            $data['percentage'] = '66.6';
        } elseif ($data['chargebacks_percentage'] <= 1 || $data['refund_percentage'] <= 5 || $data['flagged_percentage'] <= 3 || $data['retrieval_percentage'] <= 1) {
            $data['text'] = 'Low';
            $data['color'] = 'success';
            $data['percentage'] = '33.3';
        }

        return $data;

    }

    public function getSettlementReportSuccessDeclined($user_id, $start_date, $end_date = "")
    {

        if (empty($end_date)) {
            $end_date = date("Y-m-d");
        }

        $transactions = static::select(
            \DB::raw('COUNT(*) as total'),
            \DB::raw('SUM(amount) as amount'),
            'currency',
            'card_type',
            'status'
        )
            ->where('user_id', $user_id)
            ->where(\DB::raw('DATE(transaction_date)'), ">=", date("Y-m-d", strtotime($start_date)))
            ->where(\DB::raw('DATE(transaction_date)'), "<=", date("Y-m-d", strtotime($end_date)))
            ->whereNotIn('payment_gateway_id', [1, 2])
            ->whereIn('status', [0, 1])
            ->whereNull('deleted_at')
            ->groupBy(['currency', 'card_type', 'status'])
            ->get();

        return $transactions;

    }

    public function getSettlementReportChbTransactions($user_id, $start_date, $end_date = "")
    {

        if (empty($end_date)) {
            $end_date = date("Y-m-d");
        }

        $transactions = static::select(
            \DB::raw('COUNT(*) as total'),
            \DB::raw('SUM(amount) as amount'),
            'currency',
        )
            ->where('user_id', $user_id)
            ->where(\DB::raw('DATE(transaction_date)'), ">=", date("Y-m-d", strtotime($start_date)))
            ->where(\DB::raw('DATE(transaction_date)'), "<=", date("Y-m-d", strtotime($end_date)))
            ->whereNotIn('payment_gateway_id', [1, 2])
            ->whereIn('status', [0, 1])
            ->where('chargebacks', '=', '1')
            ->whereNull('deleted_at')
            ->groupBy(['currency', 'card_type', 'status'])
            ->get();

        return $transactions;

    }
    public function getSettlementReportSuspiciousTransactions($user_id, $start_date, $end_date = "")
    {

        if (empty($end_date)) {
            $end_date = date("Y-m-d");
        }

        $transactions = static::select(
            \DB::raw('COUNT(*) as total'),
            \DB::raw('SUM(amount) as amount'),
            'currency',
        )
            ->where('user_id', $user_id)
            ->where(\DB::raw('DATE(transaction_date)'), ">=", date("Y-m-d", strtotime($start_date)))
            ->where(\DB::raw('DATE(transaction_date)'), "<=", date("Y-m-d", strtotime($end_date)))
            ->whereNotIn('payment_gateway_id', [1, 2])
            ->whereIn('status', [0, 1])
            ->where('is_flagged', '=', '1')
            ->whereNull('deleted_at')
            ->groupBy(['currency', 'card_type', 'status'])
            ->get();

        return $transactions;

    }
    public function getSettlementReportRefundTransactions($user_id, $start_date, $end_date = "")
    {

        if (empty($end_date)) {
            $end_date = date("Y-m-d");
        }

        $transactions = static::select(
            \DB::raw('COUNT(*) as total'),
            \DB::raw('SUM(amount) as amount'),
            'currency',
        )
            ->where('user_id', $user_id)
            ->where(\DB::raw('DATE(transaction_date)'), ">=", date("Y-m-d", strtotime($start_date)))
            ->where(\DB::raw('DATE(transaction_date)'), "<=", date("Y-m-d", strtotime($end_date)))
            ->whereNotIn('payment_gateway_id', [1, 2])
            ->whereIn('status', [0, 1])
            ->where('refund', '=', '1')
            ->whereNull('deleted_at')
            ->groupBy(['currency', 'card_type', 'status'])
            ->get();

        return $transactions;

    }

    public function getSettlementReportRetrivalTransactions($user_id, $start_date, $end_date = "")
    {

        if (empty($end_date)) {
            $end_date = date("Y-m-d");
        }

        $transactions = static::select(
            \DB::raw('COUNT(*) as total'),
            \DB::raw('SUM(amount) as amount'),
            'currency',
        )
            ->where('user_id', $user_id)
            ->where(\DB::raw('DATE(transaction_date)'), ">=", date("Y-m-d", strtotime($start_date)))
            ->where(\DB::raw('DATE(transaction_date)'), "<=", date("Y-m-d", strtotime($end_date)))
            ->whereNotIn('payment_gateway_id', [1, 2])
            ->whereIn('status', [0, 1])
            ->where('is_retrieval', '=', '1')
            ->whereNull('deleted_at')
            ->groupBy(['currency', 'card_type', 'status'])
            ->get();

        return $transactions;

    }

    public function getSettlementReportpreArbitrationTransactions($user_id, $start_date, $end_date = "")
    {

        if (empty($end_date)) {
            $end_date = date("Y-m-d");
        }

        $transactions = static::select(
            \DB::raw('COUNT(*) as total'),
            \DB::raw('SUM(amount) as amount'),
            'currency',
        )
            ->where('user_id', $user_id)
            ->where(\DB::raw('DATE(transaction_date)'), ">=", date("Y-m-d", strtotime($start_date)))
            ->where(\DB::raw('DATE(transaction_date)'), "<=", date("Y-m-d", strtotime($end_date)))
            ->whereNotIn('payment_gateway_id', [1, 2])
            ->whereIn('status', [0, 1])
            ->where('is_pre_arbitration', '=', '1')
            ->whereNull('deleted_at')
            ->groupBy(['currency', 'card_type', 'status'])
            ->get();

        return $transactions;

    }

}