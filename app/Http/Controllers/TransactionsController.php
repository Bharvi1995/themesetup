<?php

namespace App\Http\Controllers;

use App\Mail\AdminRefundNotification;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Session;

use Mail;
use App\User;
use App\Transaction;
use App\GenerateReport;
use App\Merchantapplication;
use App\Exports\TransactionsExport;
use App\Exports\TestTransactionsExport;
use App\Exports\RecurringTransactionsExport;
use App\Exports\SubTransactionsExport;
use App\Exports\RetrievalTransactionExport;
use App\Exports\RefundTransactionsExport;
use App\Exports\ChargebacksTransactionExport;
use App\Exports\FlaggedTransactionExport;
use Maatwebsite\Excel\Facades\Excel;
use App\TransactionsDocumentUpload;
use Illuminate\Support\Facades\Storage;
use App\Mail\UserClaimRefund;
use App\Mail\TransactionSuccessMail;
use DB;

class TransactionsController extends HomeController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->user = new User;
        $this->Transaction = new Transaction;
        $this->GenerateReport = new GenerateReport;

        $this->moduleTitleS = 'User Transaction';
        $this->moduleTitleP = 'front.transactions';

        view()->share('moduleTitleP', $this->moduleTitleP);
        view()->share('moduleTitleS', $this->moduleTitleS);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $user = Auth::user()->main_user_id ? Auth::user()->main_user_id : Auth::user()->id;
        $input["user_id"] = $user;
        $data = $this->Transaction->getAllMerchantTransactionData($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->get();
        return view($this->moduleTitleP . '.index', compact('payment_gateway_id', 'data', 'noList'));
    }

    public function chargebacks(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $user = Auth::user()->main_user_id ? Auth::user()->main_user_id : Auth::user()->id;
        $input["user_id"] = $user;
        $data = $this->Transaction->getChargebacksMerchantTransactionData($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->get();
        return view($this->moduleTitleP . '.chargebacks', compact('payment_gateway_id', 'data', 'noList'));
    }

    public function refunds(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $user = Auth::user()->main_user_id ? Auth::user()->main_user_id : Auth::user()->id;
        $input["user_id"] = $user;
        $data = $this->Transaction->getRefundsMerchantTransactionData($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->get();
        return view($this->moduleTitleP . '.refunds', compact('payment_gateway_id', 'data', 'noList'));
    }

    public function flagged(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $user = Auth::user()->main_user_id ? Auth::user()->main_user_id : Auth::user()->id;
        $input["user_id"] = $user;
        $data = $this->Transaction->getFlaggedMerchantTransactionData($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->get();
        return view($this->moduleTitleP . '.flagged', compact('payment_gateway_id', 'data', 'noList'));
    }

    public function testTransactions(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));

        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $user = Auth::user()->main_user_id ? Auth::user()->main_user_id : Auth::user()->id;
        $input["user_id"] = $user;
        $data = $this->Transaction->getMerchantTestTransactionData($input, $noList);
        return view($this->moduleTitleP . '.test_transactions', compact('data', 'noList'));
    }

    public function transactionDetails(Request $request)
    {
        $data = $this->Transaction->findData($request->id);
        if ($data->user_id != \Auth::user()->id && $data->user_id != Auth::user()->main_user_id) {
            return response()->json([
                'success' => '1',
                'html' => ''
            ]);
        }
        $html = view('partials.transactions.single-transaction', compact('data'))->render();
        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function show($id)
    {
        $data = $this->Transaction->findData($id);
        if (empty($data)) {
            abort(404);
        }
        if ($data->user_id != \Auth::user()->id) {
            return redirect()->back();
        }
        return view($this->moduleTitleP . '.show', compact('data'));
    }

    public function showDocumentChargebacks(Request $request)
    {
        $data = TransactionsDocumentUpload::where('transaction_id', $request->id)->where('files_for', 'chargebacks')->first();
        $id = $request->id;
        $html = view('front.transactions.show_document_chargebacks', compact('data', 'id'))->render();
        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function downloadDocumentsUploade(Request $request)
    {
        return Storage::disk('s3')->download('uploads/transactionDocumentsUpload/' . $request->file, $request->file);
    }

    public function deleteDocumentChargebacks(Request $request, $id)
    {
        $input = $request->all();
        $data = TransactionsDocumentUpload::where('transaction_id', $id)->where('files_for', 'chargebacks')->first();
        if (!is_null($data)) {
            $messages = json_decode($data['files']);
            $myArray = [];
            foreach ($messages as $key => $value) {
                if ($value != $input['id']) {
                    $myArray[] = $value;
                }
            }
            $data->update(['files' => json_encode($myArray)]);
        }
        try {
            Storage::disk('s3')->delete('uploads/transactionDocumentsUpload/' . $input['id']);
            return response()->json([
                'success' => true,
            ]);
            \Session::put('success', 'Document Delete Successfully!');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function showDocumentFlagged(Request $request)
    {
        $data = TransactionsDocumentUpload::where('transaction_id', $request->id)->where('files_for', 'flagged')->first();
        $id = $request->id;
        $html = view('front.transactions.show_document_flagged', compact('data', 'id'))->render();
        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function deleteDocumentFlagged(Request $request, $id)
    {
        $input = $request->all();
        $data = TransactionsDocumentUpload::where('transaction_id', $id)->where('files_for', 'flagged')->first();
        if (!is_null($data)) {
            $messages = json_decode($data['files']);
            $myArray = [];
            foreach ($messages as $key => $value) {
                if ($value != $input['id']) {
                    $myArray[] = $value;
                }
            }
            $data->update(['files' => json_encode($myArray)]);
        }
        try {
            Storage::disk('s3')->delete('uploads/transactionDocumentsUpload/' . $input['id']);
            return response()->json([
                'success' => true,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function retrieval(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method'));
        if (isset($input['noList'])) {
            $noList = $input['noList'];
        } else {
            $noList = 10;
        }
        $user = Auth::user()->main_user_id ? Auth::user()->main_user_id : Auth::user()->id;
        $input["user_id"] = $user;
        $data = $this->Transaction->getRetrivalMerchantTransactionData($input, $noList);
        $payment_gateway_id = \DB::table('middetails')->get();
        return view($this->moduleTitleP . '.retrieval', compact('payment_gateway_id', 'data', 'noList'));
    }

    public function showDocumentRetrieval(Request $request)
    {
        $data = TransactionsDocumentUpload::where('transaction_id', $request->id)->where('files_for', 'retrieval')->first();
        $id = $request->id;
        $html = view('front.transactions.show_document_retrieval', compact('data', 'id'))->render();
        return response()->json([
            'success' => '1',
            'html' => $html
        ]);
    }

    public function deleteDocumentRetrieval(Request $request, $id)
    {
        $input = $request->all();
        $data = TransactionsDocumentUpload::where('transaction_id', $id)->where('files_for', 'retrieval')->first();
        if (!is_null($data)) {
            $messages = json_decode($data['files']);
            $myArray = [];
            foreach ($messages as $key => $value) {
                if ($value != $input['id']) {
                    $myArray[] = $value;
                }
            }
            $data->update(['files' => json_encode($myArray)]);
        }
        try {
            Storage::disk('s3')->delete('uploads/transactionDocumentsUpload/' . $input['id']);
            return response()->json([
                'success' => true,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function refund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refund_reason' => 'required',
        ]);
        $input = \Arr::except($request->all(), array('_token', '_method', 'id'));
        $input['refund'] = '1';
        $input['refund_date'] = date("Y-m-d H:i:s", time());
        if ($validator->passes()) {
            if ($this->Transaction->updateData($request->get('id'), $input)) {
                try {
                    $transaction = DB::table('transactions')->select("order_id", "refund_reason")->where("id", $request->get('id'))->first();
                    Mail::to(Auth::user()->email)->queue(new UserClaimRefund(Transaction::find($request->get('id'))));

                } catch (\Exception $e) {
                    return response()->json(['success' => '0']);
                }
                return response()->json(['success' => '1']);
            } else {
                return response()->json(['success' => '0']);
            }
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function sendmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_address' => 'required|email',
        ]);
        $input = \Arr::except($request->all(), array('_token', '_method', 'id'));
        if ($validator->passes()) {
            $input = Transaction::where("id", $request->id)->first();
            try {
                Mail::to($request->email_address)->send(new TransactionSuccessMail($input->toArray()));
            } catch (\Exception $e) {
            }
            return response()->json(['success' => '1']);
        }
        return response()->json(['errors' => $validator->errors()]);
    }

    public function exportAllTransactions(Request $request)
    {
        return Excel::download(new TransactionsExport, 'Transaction_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportRefunds(Request $request)
    {
        return Excel::download(new RefundTransactionsExport, 'Refund_Transaction_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportChargebacks(Request $request)
    {
        return Excel::download(new ChargebacksTransactionExport, 'Chargebacks_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportFlagged(Request $request)
    {
        return Excel::download(new FlaggedTransactionExport, 'Flagged_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportTestTransactions(Request $request)
    {
        return Excel::download(new TestTransactionsExport, 'Test_Transactions_Excel_' . date('d-m-Y') . '.xlsx');
    }

    public function exportRetrieval(Request $request)
    {
        return Excel::download(new RetrievalTransactionExport, 'Retrieval_Transaction_Excel_' . date('d-m-Y') . '.xlsx');
    }
}