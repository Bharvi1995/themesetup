<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateCentpayPendingTransactions;
use App\Mail\SendJobsCountMail;

use App\User;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Jobs\TestJob;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use DB;




class AdminTestController extends Controller
{
    public function testRpAgreement()
    {
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml(view('admin.agents.agreement_PDF'));

        $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');
        $dompdf->render();
        $dompdf->stream('test', array("Attachment" => 0));
    }

    public function testAgreement()
    {
        $user = User::whereNotNull('setup_fee')->whereNotNull('transaction_fee')->first();
        if (isset($user)) {
            view()->share('data', $user);
            $options = new Options();
            $options->setIsRemoteEnabled(true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml(view('admin.applications.test_agreement_PDF'));

            $dompdf->setPaper([0, 0, 800.98, 700.85], 'landscape');
            $dompdf->render();
            $dompdf->stream('test', array("Attachment" => 0));
        } else {
            return response()->json(["status" => 200, "message" => "No merchant found with fee"]);
        }
    }

    public function testEmail($email)
    {
        Mail::to($email)->send(new TestMail());
        return response()->json(["status" => 200, 'msg' => "Email sent successfully!"]);
    }

    public function testJobEmail($email)
    {
        TestJob::dispatch($email);
        return response()->json(["status" => 200, 'msg' => "Email sent successfully!"]);
    }

    // * Send Jobs and failed job notification
    public function sendJobsCount(Request $request)
    {
        try {
            if ($request->password != 'ffg5431234naSdkc23VC111sShiu4fsfd425') {
                exit();
            }
            $jobsCount = DB::table('jobs')->count();
            $failedJobsCount = DB::table('failed_jobs')->count();
            if ($jobsCount > 0 || $failedJobsCount > 0) {
                Mail::to("test@gmail.com")->cc(['test@gmail.com', 'test@gmail.com'])->send(new SendJobsCountMail($jobsCount, $failedJobsCount));
            }
            return response()->json(["status" => 200, "message" => "Email Sent successfully!"]);
        } catch (\Throwable $th) {
            return response()->json(["status" => 500, "message" => "Something went wrong!."]);

        }

    }

    // * update the Centpay Pending transactions 
    public function updateCentpayTransaction()
    {

        UpdateCentpayPendingTransactions::dispatch();
        return response()->json(["status" => 200, "message" => "Job Added successfully!"], 200);
    }

    public function payoCallback(Request $request, $id)
    {
        $response = $request->all();

        return view('test.payo', compact('response', 'id'));
    }

}