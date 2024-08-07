<?php

namespace App;

use DB;
use Mail;
use App\Mail\SendDynamicMail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    /**
	 * The attribute that assign the database table.
	 *
	 * @var array
	 */
    protected $table = 'warnings';

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = array();

    // ================================================
    /*  method : getWarnings
    * @ param  :
    * @ Description : get warnings for users and admins
    */// ==============================================
    function getWarnings($user_id = null, $type = null, $limit = null)
    {
        $warnings = static::orderBy('id', 'desc');
                if ($user_id != null) {
                    $warnings = $warnings->where('user_id', $user_id);
                }
                if ($limit != null) {
                    $warnings = $warnings->limit($limit);
                }
                $warnings = $warnings->get();

        return $warnings;
    }

    // ================================================
    /*  method : getWarnings
    * @ param  :
    * @ Description : get warnings for users and admins
    */// ==============================================
    function getWarningWithUser($limit = null)
    {
        $warnings = static::orderBy('warnings.id', 'desc')
                ->select('warnings.*', 'merchantapplications.company_name')
                ->join('merchantapplications', 'warnings.user_id', 'merchantapplications.user_id');
                if ($limit != null) {
                    $warnings = $warnings->limit($limit);
                }
                $warnings = $warnings->get();

        return $warnings;
    }

    // ================================================
    /*  method : warningLimitOfTransactions
    * @ param  :
    * @ Description :
    */// ==============================================
    public function warningLimitOfTransactions($user_id)
    {
        $start_date = Carbon::now()->subDays(30);
        $end_date = Carbon::now();

        // if(isset($input['start_date']) && $input['start_date'] != '') {
        //     $start_date = date('Y-m-d',strtotime($input['start_date']));
        // }
        // if(isset($input['end_date']) && $input['end_date'] != '') {
        //     $end_date = date('Y-m-d',strtotime($input['end_date']));
        // }

        // total no. of transactions
        $total_transactions = DB::table('transactions')
            ->where('user_id', $user_id)
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->count();

        // if total no. of users transactions > 20
        if ($total_transactions >= 20) {
            // total no. of refund transactions
            $refund_transactions = DB::table('transactions')
                ->where('user_id', $user_id)
                ->where('refund', '1')
                ->where('resubmit_transaction', '<>', '2')
                ->where('is_batch_transaction', '0')
                ->whereNull('transactions.deleted_at')
                ->whereBetween('created_at', [$start_date, $end_date])
                ->count();

            // total no. of chargeback transactions
            $chargebacks_transactions = DB::table('transactions')
                ->where('user_id', $user_id)
                ->where('chargebacks', '1')
                ->where('resubmit_transaction', '<>', '2')
                ->where('is_batch_transaction', '0')
                ->whereNull('transactions.deleted_at')
                ->whereBetween('created_at', [$start_date, $end_date])
                ->count();

            // total no. of declined transactions
            $declined_transactions = DB::table('transactions')
                ->where('user_id', $user_id)
                ->where('status', '0')
                ->where('chargebacks', '<>', '1')
                ->where('refund', '<>', '1')
                ->where('is_flagged', '<>', '1')
                ->where('resubmit_transaction', '<>', '2')
                ->where('is_batch_transaction', '0')
                ->whereBetween('created_at', [$start_date, $end_date])
                ->count();

            // total no. of flagged transactions
            $flagged_transactions = DB::table('transactions')
                ->where('user_id', $user_id)
                ->where('is_flagged', '1')
                ->where('resubmit_transaction', '<>', '2')
                ->where('is_batch_transaction', '0')
                ->whereNull('transactions.deleted_at')
                ->whereBetween('created_at', [$start_date, $end_date])
                ->count();

            // total no. of retrieval transactions
            $retrieval_transactions = DB::table('transactions')
                ->where('user_id', $user_id)
                ->where('is_retrieval', '1')
                ->whereNull('deleted_at')
                ->whereBetween('retrieval_date', [$start_date, $end_date])
                ->count();

            // refund MID suspend limit
            if ($refund_transactions * 100 / $total_transactions >= 5) {
            	// generate warning message
            	$warnings[] = [
            		'level' => 1,
                    'user_id' => $user_id,
            		'type' => 'refund_suspend_limit',
            		'body' => 'Your MID has been temporarily suspended due to refund transactions limit. Kindly contact info.paylaksa@gmail.com for further enquiry.',
            		'remark' => 'refund transactions '.$refund_transactions.'/'.$total_transactions,
            		'created_at' => date('Y-m-d H:i:s'),
            		'updated_at' => date('Y-m-d H:i:s'),
            	];
            	// suspend user MID
            	$mid_suspend = true;
        	// refund MID warning limit
            } elseif ($refund_transactions * 100 / $total_transactions >= 5) {
	        	// generate warning message
	        	$warnings[] = [
                    'level' => 0,
	        		'user_id' => $user_id,
	        		'type' => 'refund_warning_limit',
	        		'body' => 'Your refund transactions are to high.',
	        		'remark' => 'refund transactions '.$refund_transactions.'/'.$total_transactions,
	        		'created_at' => date('Y-m-d H:i:s'),
            		'updated_at' => date('Y-m-d H:i:s'),
	        	];
            }

            // chargebacks MID suspend limit
            if ($chargebacks_transactions * 100 / $total_transactions >= 2) {
            	// generate warning message
            	$warnings[] = [
                    'level' => 1,
            		'user_id' => $user_id,
            		'type' => 'chargebacks_suspend_limit',
            		'body' => 'Your MID has been temporarily suspended due to chargebacks transactions limit. Kindly contact info.paylaksa@gmail.com for further enquiry.',
            		'remark' => 'chargebacks transactions '.$chargebacks_transactions.'/'.$total_transactions,
            		'created_at' => date('Y-m-d H:i:s'),
            		'updated_at' => date('Y-m-d H:i:s'),
            	];
            	// suspend user MID
            	$mid_suspend = true;
        	// chargebacks MID warning limit
            } elseif ($chargebacks_transactions * 100 / $total_transactions >= 1) {
	        	// generate warning message
	        	$warnings[] = [
                    'level' => 0,
	        		'user_id' => $user_id,
	        		'type' => 'chargebacks_warning_limit',
	        		'body' => 'Your chargebacks transactions are to high.',
	        		'remark' => 'chargebacks transactions '.$chargebacks_transactions.'/'.$total_transactions,
	        		'created_at' => date('Y-m-d H:i:s'),
            		'updated_at' => date('Y-m-d H:i:s'),
	        	];
            }

            // flagged MID suspend limit
            if ($flagged_transactions * 100 / $total_transactions >= 10) {
            	// generate warning message
            	$warnings[] = [
                    'level' => 1,
            		'user_id' => $user_id,
            		'type' => 'flagged_suspend_limit',
            		'body' => 'Your MID has been temporarily suspended due to suspicious transactions limit. Kindly contact info.paylaksa@gmail.com for further enquiry.',
            		'remark' => 'suspicious transactions '.$flagged_transactions.'/'.$total_transactions,
            		'created_at' => date('Y-m-d H:i:s'),
            		'updated_at' => date('Y-m-d H:i:s'),
            	];
            	// suspend user MID
            	$mid_suspend = true;
        	// flagged MID warning limit
            } elseif ($flagged_transactions * 100 / $total_transactions >= 3) {
            	// generate warning message
            	$warnings[] = [
                    'level' => 0,
            		'user_id' => $user_id,
            		'type' => 'flagged_warning_limit',
            		'body' => 'Your suspicious transactions are to high.',
            		'remark' => 'suspicious transactions '.$flagged_transactions.'/'.$total_transactions,
            		'created_at' => date('Y-m-d H:i:s'),
            		'updated_at' => date('Y-m-d H:i:s'),
            	];
            }

            // declined MID suspend limit
            if ($declined_transactions * 100 / $total_transactions >= 50) {
            	// generate warning message
            	$warnings[] = [
                    'level' => 1,
            		'user_id' => $user_id,
            		'type' => 'declined_suspend_limit',
            		'body' => 'Your MID has been temporarily suspended due to declined transactions limit. Kindly contact info.paylaksa@gmail.com for further enquiry.',
            		'remark' => 'declined transactions '.$declined_transactions.'/'.$total_transactions,
            		'created_at' => date('Y-m-d H:i:s'),
            		'updated_at' => date('Y-m-d H:i:s'),
            	];
            	// suspend user MID
            	$mid_suspend = true;
                return response()->json(['suspend_declined_reason' => 'Your Fail transactiond is greater than limit.']);
            }

            // retrieval MID warning limit
            if ($retrieval_transactions * 100 / $total_transactions == 1) {
            	// generate warning message
            	$warnings[] = [
                    'level' => 0,
            		'user_id' => $user_id,
            		'type' => 'retrieval_warning_limit',
            		'body' => 'Your retrieval transactions are to high.',
            		'remark' => 'retrieval transactions '.$retrieval_transactions.'/'.$total_transactions,
            		'created_at' => date('Y-m-d H:i:s'),
            		'updated_at' => date('Y-m-d H:i:s'),
            	];
            }

            // insert into warning table
            if (!empty($warnings)) {

            	$user_mail = User::where('id', $user_id)->value('email');

            	// create array
            	$mail_array = [
            		'subject' => 'You have warning,',
            		'html' => 'emails.sendDynamicEmail',
            		'message' => 'You have get new warning.',
            		'url' => '#',
            	];

            	Mail::to('test@gmail.com')->send(new SendDynamicMail($mail_array));

            	Warning::insert($warnings);
            }

            // suspend MID
            if (isset($mid_suspend)) {
            	User::where('id', $user_id)->update(['mid' => 0]);
            }
        }
    }
}
