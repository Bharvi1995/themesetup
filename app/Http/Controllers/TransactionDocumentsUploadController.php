<?php

namespace App\Http\Controllers;

use Auth;
use URL;
use Input;
use File;
use View;
use Session;
use Redirect;
use Validator;
use App\User;
use App\Transaction;
use App\TransactionsDocumentUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionDocumentsUploadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = new User;
        $this->Transaction = new Transaction;
    }

    public function transactionDocumentsUpload(Request $request)
    {
        $data = Transaction::where('id', $request->transactionId)->where('transactions_token', $request["token"])->first();
        if ($data == null) {
            return redirect()->route('login');
        } else {
            return view('front.transactionDocumentsUpload.index', compact('request', 'data'));
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $input = \Arr::except($input, array('_token', '_method'));
        $validator = Validator::make($request->all(), [
            'files.*' => 'required|mimes:jpg,jpeg,png,pdf,zip|max:35840'
        ]);
        $messages = $validator->messages();
        if ($validator->passes()) {
            try {
                if ($request->hasFile('files')) {
                    $exists = TransactionsDocumentUpload::where('transaction_id', $input['transaction_id'])->where('files_for', $input['files_for'])->first();
                    if ($exists == null) {
                        if ($request->hasFile('files')) {
                            $files = $request->file('files');
                            foreach ($request->file('files') as $key => $value) {
                                $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                                $imageName = $imageName . '.' . $files[$key]->getClientOriginalExtension();
                                $filePath = '/uploads/transactionDocumentsUpload/' . $imageName;
                                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                                $input['fileso'][] = $imageName;
                            }
                            $input['files'] = json_encode($input['fileso']);
                        }
                        $input = \Arr::except($input, array(
                            'fileso'
                        ));
                        TransactionsDocumentUpload::create($input);
                        $transaction = Transaction::where('transactions.id', $input['transaction_id'])->first();
                        \Session::put('success', 'Document Upload successfully.');
                    } else {
                        \Session::put('error', 'Document Already Uploaded.');
                    }
                } else {
                    \Session::put('error', 'Please select the document');
                }
                return redirect()->back();
            } catch (\Exception $e) {
                \Session::put('error', 'Something went wrong.');
                return redirect()->back();
            }
        } else {
            $str = "";
            foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                foreach ($messages as $message) {
                    $str = $message;
                }
            }
            \Session::put('error', $message);
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $input = \Arr::except($input, array('_token', '_method'));
        $validator = Validator::make($request->all(), [
            'files.*' => 'required|mimes:jpg,jpeg,png,pdf,zip|max:35840'
        ]);

        if ($validator->passes()) {
            try {
                $exists = TransactionsDocumentUpload::where('transaction_id', $input['transaction_id'])->where('files_for', $input['files_for'])->first();
                if ($request->hasFile('files')) {
                    $files = $request->file('files');

                    foreach ($request->file('files') as $key => $value) {
                        $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                        $imageName = $imageName . '.' . $files[$key]->getClientOriginalExtension();
                        $filePath = '/uploads/transactionDocumentsUpload/' . $imageName;
                        Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                        $input['fileso'][] = $imageName;
                    }

                    $input['files'] = json_encode($input['fileso']);
                }
                $input = \Arr::except($input, array(
                    'fileso'
                ));
                if ($exists == null) {
                    TransactionsDocumentUpload::create($input);
                    $transaction = Transaction::where('transactions.id', $input['transaction_id'])->first();
                    \Session::put('success', 'Document Upload successfully.');
                } else {
                    if ($exists->files == '') {
                        $fileData = [];
                    } else {
                        $fileData = json_decode($exists->files, true);
                    }
                    $fileData1 = json_decode($input['files'], true);
                    $json_merge = array_merge($fileData, $fileData1);
                    $json_merge = json_encode($json_merge);
                    TransactionsDocumentUpload::where('id', $exists->id)->update(['files' => $json_merge]);
                    \Session::put('success', 'Document Upload successfully.');
                }
                return redirect()->back();
            } catch (\Exception $e) {
                \Session::put('error', 'Something went wrong.');
                return redirect()->back();
            }
        } else {
            $str = "";
            foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                foreach ($messages as $message) {
                    $str = $message;
                }
            }
            \Session::put('error', $message);
            return redirect()->back();
        }
    }


    public function displayImage($folder, $filename)
    {
        $path = env('AWS_URL') . 'uploads/' . $folder . "/" . $filename;
        Storage::disk('s3')->url($path);
        return Storage::disk('s3')->get($path);
    }
}
