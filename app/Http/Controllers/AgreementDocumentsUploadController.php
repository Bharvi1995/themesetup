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
use App\AgreementDocumentUpload;
use App\RpAgreementDocumentUpload;
use App\Application;
use App\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgreementDocumentsUploadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = new User;
    }

    public function index(Request $request)
    {
        $data = AgreementDocumentUpload::where('user_id', $request->userId)
            ->where('token', $request->token)->first();
        if ($data == null) {
            return redirect()->route('login');
        } else {
            return view('front.agreementDocumentsUpload.index', compact('request', 'data'));
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
                    $exists = AgreementDocumentUpload::where('user_id', $input['user_id'])
                        ->where('token', $input['tokenId'])->first();
                    if (empty($exists->files)) {
                        if ($request->hasFile('files')) {
                            $files = $request->file('files');
                            foreach ($request->file('files') as $key => $value) {
                                $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                                $imageName = $imageName . '.' . $files[$key]->getClientOriginalExtension();
                                $filePath = 'uploads/agreementDocumentsUpload/' . $imageName;
                                Storage::disk('s3')->put($filePath, file_get_contents($value->getRealPath()));
                                $input['files'] = $filePath;
                            }
                        }

                        AgreementDocumentUpload::where('user_id', $input['user_id'])
                            ->where('token', $input['tokenId'])
                            ->update(['files' => $input['files'], 'reassign_reason' => NULL]);
                        if (empty($exists->reassign_reason)) {
                            Application::where('user_id', $input['user_id'])->update(['status' => '11']);
                        } else {
                            $application = Application::where('user_id', $input['user_id'])->first();
                           
                        }

                        \Session::put('success', 'Your agreement has been successfully uploaded.');
                    } else {
                        \Session::put('error', 'Your agreement already uploaded.');
                    }
                } else {
                    \Session::put('error', 'Please select your signed aggrement');
                }
                return redirect()->back();
            } catch (\Exception $e) {
                \Session::put('error', 'Something went wrong with your request. Kindly try again');
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

    public function uploadRP(Request $request)
    {
        $data = RpAgreementDocumentUpload::where('rp_id', $request->rpId)
            ->where('token', $request->token)->first();
        if ($data == null) {
            return redirect()->route('rp/login');
        } else {
            return view('agent.agreementDocumentsUpload.index', compact('request', 'data'));
        }
    }

    public function storeRP(Request $request)
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
                    $exists = RpAgreementDocumentUpload::where('rp_id', $input['rp_id'])
                        ->where('token', $input['tokenId'])->first();
                    if (empty($exists->files)) {
                        if ($request->hasFile('files')) {
                            $files = $request->file('files');

                            $imageName = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                            $imageName = $imageName . '.' . $files->getClientOriginalExtension();
                            $filePath = 'uploads/agreementDocumentsUpload/' . $imageName;
                            Storage::disk('s3')->put($filePath, file_get_contents($files->getRealPath()));
                            $input['files'] = $filePath;
                        }

                        RpAgreementDocumentUpload::where('rp_id', $input['rp_id'])
                            ->where('token', $input['tokenId'])
                            ->update(['files' => $input['files'], 'reassign_reason' => NULL]);

                        $agent = Agent::where('id', $input['rp_id'])->first();

                        $notification = [
                            'user_id' => '1',
                            'sendor_id' => $input['rp_id'],
                            'type' => 'admin',
                            'title' => 'RP Agreement Submitted',
                            'body' => $agent->name . ' agreement have been submitted.',
                            'url' => '/admin/agents',
                            'is_read' => '0'
                        ];

                        $realNotification = addNotification($notification);

                        \Session::put('success', 'Your agreement has been successfully uploaded.');
                    } else {
                        \Session::put('error', 'Document Already Uploaded.');
                    }
                } else {
                    \Session::put('error', 'Please select the document');
                }
                return redirect()->back();
            } catch (\Exception $e) {
                \Session::put('error', 'Something went wrong with your request. Kindly try again');
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
}
