<?php

namespace App\Jobs;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\AdminAction;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Mail\AgreementSentMailRP;
use App\RpAgreementDocumentUpload;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendRpAgreementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rp, $adminId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($rp, $adminId)
    {
        $this->rp = $rp;
        $this->adminId = $adminId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token = $this->rp->id . Str::random(32);
        $data['url'] = URL::to('/') . '/rp-agreement-documents-upload?rpId=' . $this->rp->id . '&token=' . $token;

        // $add_buy_rate = $this->rp->add_buy_rate;
        // $add_buy_rate_master = $this->rp->add_buy_rate_master;
        // $add_buy_rate_amex = $this->rp->add_buy_rate_amex;
        // $add_buy_rate_discover = $this->rp->add_buy_rate_discover;

        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml(view('admin.agents.agreement_PDF'));

        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');

        $dompdf->render();

        $filePath = 'uploads/agreement_' . $this->rp->id . '/agreement.pdf';
        Storage::put($filePath, $dompdf->output());
        Log::info(["rp-agreement-local" => $data]);
        Storage::disk('s3')->put($filePath, $dompdf->output());
        Log::info(["rp-agreement-s3" => $data]);
        $data['file'] = getS3Url($filePath);

        Mail::to($this->rp->email)->queue(new AgreementSentMailRP($data));

        RpAgreementDocumentUpload::create(['rp_id' => $this->rp->id, 'token' => $token, 'sent_files' => $filePath]);
        $ArrRequest = ['rp_id' => $this->rp->id, 'token' => $token];
        addAdminLog(AdminAction::REFERRAL_PARTNER_AGREEMENT_STATUS, $this->rp->id, $ArrRequest, "Agreement Sent");

        $notification = [
            'user_id' => $this->rp->id,
            'sendor_id' => $this->adminId,
            'type' => 'RP',
            'title' => 'Agreement Sent',
            'body' => 'Agreement has been sent to your email.',
            'url' => '/rp/dashboard',
            'is_read' => '0'
        ];

        addNotification($notification);
    }
}
