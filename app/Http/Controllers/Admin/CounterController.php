<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    // ================================================
    /* method : index
    * @param  : 
    * @description : conter index
    */// ===============================================
    public function index()
    {
        $counter = \DB::table('counters')->get()->toArray();
        dd($counter);
    }
}
