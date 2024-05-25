<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Session;
use Validator;
use App\Agent;
use App\AdminAction;
use App\AgreementContent;
use App\RpAgreementDocumentUpload;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use File;
use URL;
use Mail;
use Storage;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Mail\AgreementSentMailRP;
use App\Mail\CrossSignedAgreementSentMailRP;

class AgreementDocumentsRPController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->agentUser = new Agent;
    }

    public function AgreementSent(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method', 'rp_id'));

        if ($this->agentUser->updateData($request->get('rp_id'), ['agreement_status' => '1'])) {

            $rp_id = $request->get('rp_id');
            $rp = Agent::where('id', $rp_id)->first();
            $token = $rp_id . \Str::random(32);
            $data['url'] = URL::to('/') . '/rp-agreement-documents-upload?rpId=' . $rp_id . '&token=' . $token;

            try {
                $options = new Options();
                $options->setIsRemoteEnabled(true);
                $dompdf = new Dompdf($options);
                $dompdf->loadHtml(view('admin.agents.agreement_PDF'));

                $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');

                $dompdf->render();

                $filePath = 'uploads/agreement_' . $rp_id . '/agreement.pdf';

                Storage::disk('s3')->put($filePath, $dompdf->output());

                $data['file'] = getS3Url($filePath);
                Mail::to($rp->email)->send(new AgreementSentMailRP($data));

                RpAgreementDocumentUpload::create(['rp_id' => $rp_id, 'token' => $token, 'sent_files' => $filePath]);
                $ArrRequest = ['rp_id' => $rp_id, 'token' => $token, 'sent_files' => $filePath];
                addAdminLog(AdminAction::REFERRAL_PARTNER_AGREEMENT_STATUS, $rp_id, $ArrRequest, "Agreement Sent");

                $notification = [
                    'user_id' => $rp_id,
                    'sendor_id' => auth()->guard('admin')->user()->id,
                    'type' => 'RP',
                    'title' => 'Agreement Sent',
                    'body' => 'Agreement has been sent to your email.',
                    'url' => '/rp/dashboard',
                    'is_read' => '0'
                ];

                $realNotification = addNotification($notification);
            } catch (\Exception $e) {
                // \Log::info($e);
                \Session::put('error', 'Soemthing wrong! try Again later.');
                return response()->json(['success' => '0']);
            }

            return response()->json(['success' => '1']);
        } else {
            return response()->json(['success' => '0']);
        }
    }

    public function AgreementReceived(Request $request)
    {
        $input = \Arr::except($request->all(), array('_token', '_method', 'rp_id'));
        if ($this->agentUser->updateData($request->get('rp_id'), ['agreement_status' => '2'])) {
            $ArrRequest = ['rp_id' => $request->get('rp_id'), 'agreement_status' => 2];
            addAdminLog(AdminAction::REFERRAL_PARTNER_AGREEMENT_STATUS, $request->get('rp_id'), $ArrRequest, "Agreement Received");
            return response()->json(['success' => '1']);
        } else {
            return response()->json(['success' => '0']);
        }
    }

    public function CrossSignedAgreementSent(Request $request)
    {
        $this->validate(
            $request,
            [
                "rp_id" => "required",
                "application_id" => "required",
                'cross_signed_agreement' => 'required|mimes:jpg,jpeg,png,pdf,txt,doc,docx,xls,xlsx,zip|max:35840'
            ],
            [
                'cross_signed_agreement.max' => 'The Agreement size may not be greater than 25 MB.'
            ]
        );
        $rp_id = $request->get('rp_id');
        $application_id = $request->get('application_id');
        $rp = Agent::where('id', $rp_id)->first();

        $AgreementDetail = RpAgreementDocumentUpload::where('rp_id', $rp_id)->first();
        if (!empty($AgreementDetail)) {

            if ($request->hasFile('cross_signed_agreement')) {
                $imageNameCertificate = time() . rand(0, 10000000000000) . pathinfo(rand(111111111111, 999999999999), PATHINFO_FILENAME);
                $imageNameCertificate = $imageNameCertificate . '.' . $request->file('cross_signed_agreement')->getClientOriginalExtension();
                $filePath = 'uploads/rp-cross-signed-agreement-' . $rp_id . '/' . $imageNameCertificate;
                Storage::disk('s3')->put($filePath, file_get_contents($request->file('cross_signed_agreement')->getRealPath()));
                $AgreementDetail->cross_signed_agreement = $filePath;
                $AgreementDetail->save();
                $data['file'] = getS3Url($filePath);
                $data['name'] = $rp->name;
                try {
                    Mail::to($rp->email)->send(new CrossSignedAgreementSentMailRP($data));
                } catch (Exception $e) {

                    notificationMsg('error', 'Something went wrong. Try Again.');
                    return redirect()->back();
                }
                notificationMsg('success', 'Agreement sent successfully.');
                return redirect()->back();
            } else {
                notificationMsg('error', 'Something went wrong. Try Again.');
                return redirect()->back();
            }
        } else {
            notificationMsg('error', 'Something went wrong. Try Again.');
            return redirect()->back();
        }
    }
}
