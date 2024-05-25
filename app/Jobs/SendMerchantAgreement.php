<?php

namespace App\Jobs;

use App\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Mail\AgreementSentMail;
use App\AgreementDocumentUpload;
use App\Application;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMerchantAgreement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user,  $appId, $adminId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user,  $appId, $adminId)
    {
        $this->user = $user;
        $this->appId = $appId;
        $this->adminId = $adminId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        view()->share('data', $this->user);
        $token = $this->user->id . Str::random(32);
        $data['url'] = URL::to('/') . '/agreement-documents-upload?userId=' . $this->user->id . '&token=' . $token;

        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('admin.applications.agreement_PDF'));


        // $dompdf->setPaper('A4', 'landscape');
        // $dompdf->setPaper([0, 0, 1000.98, 900.85], 'landscape');
        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');

        $dompdf->render();

        $filePath = 'uploads/agreement_' . $this->user->id . '/agreement.pdf';

        Storage::disk('s3')->put($filePath, $dompdf->output());

        $data['name'] = $this->user->name;
        $data['file'] = getS3Url($filePath);
        Mail::to($this->user->email)->send(new AgreementSentMail($data));
        AgreementDocumentUpload::create(['user_id' => $this->user->id, 'application_id' => $this->appId, 'token' => $token, 'sent_files' => $filePath]);

        $notification = [
            'user_id' => $this->user->id,
            'sendor_id' => $this->adminId,
            'type' => 'user',
            'title' => 'Agreement Sent',
            'body' => 'Agreement has been sent to your email.',
            'url' => '/my-application',
            'is_read' => '0'
        ];

        addNotification($notification);
    }
}
