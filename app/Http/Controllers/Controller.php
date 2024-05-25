<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function reformatDate($date, $from_format = 'm/d/Y', $to_format = 'Y-m-d') {
        $newdate="";
        $date_aux = date_create_from_format($from_format, trim($date," "));
        if(!$date_aux){
            $newdate=$date;
        }else{
            $newdate=date_format($date_aux,$to_format);
        }
        return $newdate;
    }
}
