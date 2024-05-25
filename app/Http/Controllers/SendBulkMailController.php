<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SendBulkMailController extends Controller
{
    public function sendBulkMail(Request $request)
    {
    	$details = [
    		'subject' => 'test'
    	];

    	// send all mail in the queue.
        $job = (new \App\Jobs\SendBulkQueueEmail($details))
            ->delay(
            	now()
            	->addSeconds(2)
            ); 

        dispatch($job);

        echo "Bulk mail send successfully in the background...";
    }
}
